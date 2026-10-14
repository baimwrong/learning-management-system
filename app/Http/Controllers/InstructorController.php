<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstructorController extends Controller
{
    /**
     * Instructor Overview Dashboard.
     */
    public function dashboard()
    {
        $instructor = auth()->user();
        $courseIds = $instructor->instructedCourses()->pluck('id');

        $stats = [
            'total_courses' => $courseIds->count(),
            'published_courses' => $instructor->instructedCourses()->where('is_published', true)->count(),
            'total_students' => Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id'),
            'total_enrollments' => Enrollment::whereIn('course_id', $courseIds)->count(),
        ];

        $recentCourses = $instructor->instructedCourses()->withCount(['enrollments', 'modules'])->latest()->take(5)->get();
        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)->with(['user', 'course'])->latest()->take(6)->get();

        return view('instructor.dashboard', compact('stats', 'recentCourses', 'recentEnrollments'));
    }

    /**
     * Courses List.
     */
    public function courses()
    {
        $courses = auth()->user()->instructedCourses()
            ->with(['category'])
            ->withCount(['enrollments', 'modules'])
            ->latest()
            ->paginate(10);

        return view('instructor.courses.index', compact('courses'));
    }

    /**
     * Show Course Creation Form.
     */
    public function createCourse()
    {
        $categories = Category::where('is_active', true)->get();
        return view('instructor.courses.create', compact('categories'));
    }

    /**
     * Store new Course.
     */
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'level' => 'required|in:all_levels,beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'summary' => 'required|string|max:500',
            'description' => 'required|string',
            'is_published' => 'nullable|boolean',
            'requirements' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Parse multiline string into array
        $reqs = array_filter(array_map('trim', explode("\n", $validated['requirements'] ?? '')));
        $outcomes = array_filter(array_map('trim', explode("\n", $validated['learning_outcomes'] ?? '')));

        $course = Course::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category_id' => $validated['category_id'],
            'instructor_id' => auth()->id(),
            'level' => $validated['level'],
            'price' => $validated['price'],
            'summary' => $validated['summary'],
            'description' => $validated['description'],
            'requirements' => array_values($reqs),
            'learning_outcomes' => array_values($outcomes),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('instructor.courses.curriculum', $course)
            ->with('success', "Course '{$course->title}' created! Now let's build the curriculum modules and lessons.");
    }

    /**
     * Show Course Edit Form.
     */
    public function editCourse(Course $course)
    {
        $this->authorizeCourse($course);
        $categories = Category::where('is_active', true)->get();

        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update Course.
     */
    public function updateCourse(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'level' => 'required|in:all_levels,beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'summary' => 'required|string|max:500',
            'description' => 'required|string',
            'is_published' => 'nullable|boolean',
            'requirements' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
        ]);

        $reqs = array_filter(array_map('trim', explode("\n", $validated['requirements'] ?? '')));
        $outcomes = array_filter(array_map('trim', explode("\n", $validated['learning_outcomes'] ?? '')));

        $course->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'level' => $validated['level'],
            'price' => $validated['price'],
            'summary' => $validated['summary'],
            'description' => $validated['description'],
            'requirements' => array_values($reqs),
            'learning_outcomes' => array_values($outcomes),
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Course updated successfully.');
    }

    /**
     * Course Curriculum Builder.
     */
    public function curriculum(Course $course)
    {
        $this->authorizeCourse($course);
        $course->load(['modules.lessons.quiz']);

        return view('instructor.courses.curriculum', compact('course'));
    }

    /**
     * Store new Module in curriculum.
     */
    public function storeModule(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        CourseModule::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order_number' => $course->modules()->count() + 1,
        ]);

        return back()->with('success', "Module '{$validated['title']}' added.");
    }

    /**
     * Delete Module.
     */
    public function deleteModule(CourseModule $module)
    {
        $this->authorizeCourse($module->course);
        $module->delete();

        return back()->with('success', 'Module removed.');
    }

    /**
     * Store new Lesson.
     */
    public function storeLesson(Request $request, CourseModule $module)
    {
        $this->authorizeCourse($module->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:reading,video,quiz,document',
            'duration_minutes' => 'required|integer|min:1',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'is_preview' => 'nullable|boolean',
        ]);

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'content_type' => $validated['content_type'],
            'duration_minutes' => $validated['duration_minutes'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'is_preview' => $request->boolean('is_preview'),
            'order_number' => $module->lessons()->count() + 1,
        ]);

        // If quiz content type, automatically create quiz holder
        if ($lesson->content_type === 'quiz') {
            Quiz::create([
                'course_id' => $module->course_id,
                'lesson_id' => $lesson->id,
                'title' => $lesson->title,
                'description' => 'Quiz for ' . $lesson->title,
                'time_limit_minutes' => 20,
                'passing_score_percentage' => 70,
                'max_attempts' => 3,
                'is_active' => true,
            ]);
        }

        return back()->with('success', "Lesson '{$lesson->title}' added.");
    }

    /**
     * Delete Lesson.
     */
    public function deleteLesson(Lesson $lesson)
    {
        $this->authorizeCourse($lesson->module->course);
        $lesson->delete();

        return back()->with('success', 'Lesson removed.');
    }

    /**
     * Quiz & Assessment Builder.
     */
    public function quizBuilder(Quiz $quiz)
    {
        $this->authorizeCourse($quiz->course);
        $quiz->load(['questions', 'course']);

        return view('instructor.quizzes.builder', compact('quiz'));
    }

    /**
     * Add question to Quiz.
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $this->authorizeCourse($quiz->course);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:single_choice,multiple_choice,true_false,short_answer',
            'points' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'correct_answers' => 'required',
            'explanation' => 'nullable|string',
        ]);

        // Format options array
        $formattedOptions = [];
        if (!empty($validated['options']) && is_array($validated['options'])) {
            $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
            foreach ($validated['options'] as $i => $optText) {
                if (trim($optText) !== '') {
                    $key = $letters[$i] ?? (string) ($i + 1);
                    $formattedOptions[] = [
                        'key' => $key,
                        'text' => trim($optText),
                    ];
                }
            }
        }

        // Format correct answers as array
        $correctAnswers = (array) $validated['correct_answers'];

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options' => $formattedOptions,
            'correct_answers' => $correctAnswers,
            'points' => $validated['points'],
            'explanation' => $validated['explanation'] ?? null,
            'order_number' => $quiz->questions()->count() + 1,
        ]);

        return back()->with('success', 'Question added to quiz.');
    }

    /**
     * Delete Question from Quiz.
     */
    public function deleteQuestion(QuizQuestion $question)
    {
        $this->authorizeCourse($question->quiz->course);
        $question->delete();

        return back()->with('success', 'Question deleted.');
    }

    /**
     * View Students Enrolled in Instructor's Courses.
     */
    public function students()
    {
        $courseIds = auth()->user()->instructedCourses()->pluck('id');
        $enrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->latest()
            ->paginate(15);

        return view('instructor.students.index', compact('enrollments'));
    }

    /**
     * Ensure current user owns this course (or is admin).
     */
    protected function authorizeCourse(Course $course): void
    {
        if (auth()->user()->isAdmin()) {
            return;
        }

        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this course.');
        }
    }
}
