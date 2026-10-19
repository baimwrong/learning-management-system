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
}
