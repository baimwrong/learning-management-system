<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CurriculumAndQuizTest extends TestCase
{
    use RefreshDatabase;

    protected User $instructor;
    protected User $student;
    protected Course $course;
    protected CourseModule $module;
    protected Lesson $lesson;
    protected Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructor = User::factory()->create(['role' => 'instructor', 'status' => 'active']);
        $this->student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $category = Category::create([
            'name' => 'Backend Engineering',
            'slug' => 'backend-engineering',
            'is_active' => true,
        ]);

        $this->course = Course::create([
            'title' => 'PostgreSQL 18 Mastery',
            'slug' => 'postgresql-18-mastery',
            'category_id' => $category->id,
            'instructor_id' => $this->instructor->id,
            'level' => 'intermediate',
            'summary' => 'Comprehensive masterclass on relational systems.',
            'description' => 'Deep dive into WAL, MVCC, and indexing.',
            'price' => 0.00,
            'is_published' => true,
        ]);

        $this->module = CourseModule::create([
            'course_id' => $this->course->id,
            'title' => 'Core Architecture',
            'order_number' => 1,
        ]);

        $this->lesson = Lesson::create([
            'module_id' => $this->module->id,
            'title' => 'Process Models',
            'slug' => 'process-models-123',
            'content_type' => 'quiz',
            'duration_minutes' => 10,
            'order_number' => 1,
        ]);

        $this->quiz = Quiz::create([
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson->id,
            'title' => 'Architecture Quiz',
            'passing_score_percentage' => 70,
            'max_attempts' => 2,
            'is_active' => true,
        ]);
    }

    public function test_lesson_course_has_one_through_relationship(): void
    {
        $this->assertNotNull($this->lesson->course);
        $this->assertEquals($this->course->id, $this->lesson->course->id);
    }

    public function test_instructor_can_update_module_and_lesson(): void
    {
        $response = $this->actingAs($this->instructor)->put(route('instructor.modules.update', $this->module), [
            'title' => 'Updated Core Architecture',
            'description' => 'Updated module description',
            'order_number' => 2,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('course_modules', [
            'id' => $this->module->id,
            'title' => 'Updated Core Architecture',
        ]);

        $lessonResponse = $this->actingAs($this->instructor)->put(route('instructor.lessons.update', $this->lesson), [
            'title' => 'Updated Process Models',
            'content_type' => 'reading',
            'duration_minutes' => 20,
            'content' => '<p>Updated content</p>',
        ]);

        $lessonResponse->assertSessionHas('success');
        $this->assertDatabaseHas('lessons', [
            'id' => $this->lesson->id,
            'title' => 'Updated Process Models',
            'duration_minutes' => 20,
        ]);
    }

    public function test_unenrolled_student_cannot_submit_quiz(): void
    {
        $response = $this->actingAs($this->student)->post(route('student.quiz.submit', $this->quiz), [
            'answers' => [],
        ]);

        $response->assertStatus(403);
    }

    public function test_enrolled_student_can_submit_quiz_and_attempts_are_enforced(): void
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_text' => 'Which memory area caches data pages?',
            'question_type' => 'single_choice',
            'options' => [
                ['key' => 'A', 'text' => 'shared_buffers'],
                ['key' => 'B', 'text' => 'work_mem'],
            ],
            'correct_answers' => ['A'],
            'points' => 5,
            'order_number' => 1,
        ]);

        // Attempt 1: submit correct answer
        $response1 = $this->actingAs($this->student)->post(route('student.quiz.submit', $this->quiz), [
            'answers' => [$question->id => 'A'],
        ]);

        $response1->assertRedirect();
        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'is_passed' => true,
            'earned_points' => 5,
        ]);

        // Attempt 2: submit wrong answer
        $response2 = $this->actingAs($this->student)->post(route('student.quiz.submit', $this->quiz), [
            'answers' => [$question->id => 'B'],
        ]);
        $response2->assertRedirect();

        // Attempt 3: max_attempts is 2, third attempt must be blocked
        $response3 = $this->actingAs($this->student)->post(route('student.quiz.submit', $this->quiz), [
            'answers' => [$question->id => 'A'],
        ]);

        $response3->assertRedirect(route('student.learn', $this->course->slug));
        $response3->assertSessionHas('error');
    }

    public function test_instructor_can_add_multiple_choice_question_with_comma_answers_and_student_is_graded_accurately(): void
    {
        // Instructor authors multiple choice question with comma-delimited correct answers
        $this->actingAs($this->instructor)->post(route('instructor.questions.store', $this->quiz), [
            'question_text' => 'Which are valid relational join types?',
            'question_type' => 'multiple_choice',
            'points' => 10,
            'options' => ['INNER JOIN', 'CROSS JOIN', 'SYNAPSE JOIN'],
            'correct_answers' => 'A, B',
        ]);

        $question = QuizQuestion::where('quiz_id', $this->quiz->id)
            ->where('question_type', 'multiple_choice')
            ->first();

        $this->assertNotNull($question);
        $this->assertEquals(['A', 'B'], $question->correct_answers);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
        ]);

        // Student submits matching checkbox answers ['A', 'B']
        $response = $this->actingAs($this->student)->post(route('student.quiz.submit', $this->quiz), [
            'answers' => [$question->id => ['A', 'B']],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'score_percentage' => 100.00,
            'is_passed' => true,
        ]);
    }

    public function test_student_cannot_manually_complete_quiz_lesson(): void
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
        ]);

        // Lesson is quiz content_type
        $this->assertEquals('quiz', $this->lesson->content_type);

        $response = $this->actingAs($this->student)->post(route('student.lessons.complete', $this->lesson));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('lesson_completions', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
        ]);
    }

    public function test_quiz_submission_fails_if_time_limit_exceeded(): void
    {
        $this->quiz->update(['time_limit_minutes' => 10]);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_text' => 'Sample Question',
            'question_type' => 'single_choice',
            'options' => [['key' => 'A', 'text' => 'Opt A']],
            'correct_answers' => ['A'],
            'points' => 5,
        ]);

        // Simulate start time 20 minutes ago (exceeding 10 min + 1 min grace window)
        $pastTimestamp = now()->subMinutes(20)->timestamp;

        $response = $this->actingAs($this->student)
            ->withSession(["quiz_start_{$this->quiz->id}_{$this->student->id}" => $pastTimestamp])
            ->post(route('student.quiz.submit', $this->quiz), [
                'answers' => [$question->id => 'A'],
            ]);

        $response->assertRedirect(route('student.learn', $this->course->slug));
        $response->assertSessionHas('error');
    }

    public function test_student_cannot_reply_with_parent_id_from_different_lesson(): void
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
        ]);

        $otherLesson = Lesson::create([
            'module_id' => $this->module->id,
            'title' => 'Other Lesson',
            'slug' => 'other-lesson-slug',
            'content_type' => 'reading',
        ]);

        $foreignDiscussion = \App\Models\Discussion::create([
            'lesson_id' => $otherLesson->id,
            'user_id' => $this->instructor->id,
            'comment' => 'Discussion on other lesson',
        ]);

        $response = $this->actingAs($this->student)->post(route('student.discussions.store', $this->lesson), [
            'comment' => 'Hijacked reply',
            'parent_id' => $foreignDiscussion->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('discussions', [
            'comment' => 'Hijacked reply',
        ]);
    }

    public function test_instructor_can_upload_course_thumbnail(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('thumbnail.jpg', 640, 480);

        $response = $this->actingAs($this->instructor)->post(route('instructor.courses.store'), [
            'title' => 'High Scale System Design',
            'category_id' => $this->course->category_id,
            'level' => 'advanced',
            'price' => 49.99,
            'summary' => 'System design essentials.',
            'description' => 'In depth look at distributed data stores.',
            'thumbnail' => $file,
        ]);

        $course = Course::where('title', 'High Scale System Design')->first();
        $this->assertNotNull($course);
        $this->assertNotNull($course->thumbnail);
        Storage::disk('public')->assertExists($course->thumbnail);
    }

    public function test_instructor_can_update_course_thumbnail(): void
    {
        Storage::fake('public');

        $initialFile = UploadedFile::fake()->image('initial.jpg');
        $initialPath = $initialFile->store('thumbnails', 'public');
        $this->course->update(['thumbnail' => $initialPath]);

        $newFile = UploadedFile::fake()->image('replacement.png');

        $response = $this->actingAs($this->instructor)->put(route('instructor.courses.update', $this->course), [
            'title' => $this->course->title,
            'category_id' => $this->course->category_id,
            'level' => $this->course->level,
            'price' => $this->course->price,
            'summary' => $this->course->summary,
            'description' => $this->course->description,
            'thumbnail' => $newFile,
        ]);

        $response->assertSessionHas('success');
        $this->course->refresh();
        Storage::disk('public')->assertMissing($initialPath);
        Storage::disk('public')->assertExists($this->course->thumbnail);
    }

    public function test_instructor_can_upload_lesson_attachment(): void
    {
        Storage::fake('public');

        $pdf = UploadedFile::fake()->create('cheatsheet.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->instructor)->post(route('instructor.lessons.store', $this->module), [
            'title' => 'Advanced Query Tuning Cheat Sheet',
            'content_type' => 'document',
            'duration_minutes' => 10,
            'attachment' => $pdf,
        ]);

        $lesson = Lesson::where('title', 'Advanced Query Tuning Cheat Sheet')->first();
        $this->assertNotNull($lesson);
        $this->assertNotNull($lesson->attachment_path);
        Storage::disk('public')->assertExists($lesson->attachment_path);
    }

    public function test_instructor_can_update_and_remove_lesson_attachment(): void
    {
        Storage::fake('public');

        $pdf = UploadedFile::fake()->create('old_guide.pdf', 200, 'application/pdf');
        $pdfPath = $pdf->store('attachments', 'public');
        $this->lesson->update(['attachment_path' => $pdfPath]);

        // Test remove attachment
        $response = $this->actingAs($this->instructor)->put(route('instructor.lessons.update', $this->lesson), [
            'title' => $this->lesson->title,
            'content_type' => $this->lesson->content_type,
            'duration_minutes' => $this->lesson->duration_minutes,
            'remove_attachment' => 1,
        ]);

        $this->lesson->refresh();
        $this->assertNull($this->lesson->attachment_path);
        Storage::disk('public')->assertMissing($pdfPath);
    }

    public function test_enrolled_student_can_see_attachment_in_lesson_player(): void
    {
        Storage::fake('public');

        $pdf = UploadedFile::fake()->create('handbook.pdf', 300, 'application/pdf');
        $pdfPath = $pdf->store('attachments', 'public');
        $this->lesson->update(['attachment_path' => $pdfPath]);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'progress_percentage' => 0,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->student)->get(route('student.learn', [
            $this->course->slug,
            $this->lesson->slug,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Supplementary Lesson Resource');
        $response->assertSee('Download Attachment');
        $response->assertSee(basename($pdfPath));
    }
}
