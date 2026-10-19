<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Student Portal Dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course.category')->latest()->get();

        $activeCourses = $enrollments->where('status', 'active');
        $completedCourses = $enrollments->where('status', 'completed');
        $certificates = $user->certificates()->with('course')->latest()->get();

        $stats = [
            'enrolled_count' => $enrollments->count(),
            'in_progress_count' => $activeCourses->count(),
            'completed_count' => $completedCourses->count(),
            'certificates_count' => $certificates->count(),
        ];

        return view('student.dashboard', compact('stats', 'activeCourses', 'completedCourses', 'certificates'));
    }

    /**
     * Enrolled Courses List.
     */
    public function myCourses()
    {
        $enrollments = Auth::user()->enrollments()
            ->with(['course.category', 'course.instructor'])
            ->latest()
            ->paginate(12);

        return view('student.my-courses', compact('enrollments'));
    }

    /**
     * Enroll into a Course.
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        if (!$course->is_published && !$user->isAdmin()) {
            return back()->with('error', 'This course is currently in draft mode and not accepting enrollments.');
        }

        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return redirect()->route('student.learn', $course->slug)
                ->with('info', 'You are already enrolled in this course.');
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('student.learn', $course->slug)
            ->with('success', "Enrolled successfully in '{$course->title}'! Happy learning!");
    }

    /**
     * Interactive Course Player / Learning Portal.
     */
    public function learn(string $courseSlug, ?string $lessonSlug = null)
    {
        $course = Course::where('slug', $courseSlug)
            ->with([
                'instructor',
                'modules.lessons.quiz',
            ])
            ->firstOrFail();

        $user = Auth::user();

        // Check enrollment or allow preview if admin/instructor
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment && !$user->isAdmin() && $course->instructor_id !== $user->id) {
            return redirect()->route('courses.detail', $course->slug)
                ->with('warning', 'Please enroll in this course first to access learning materials.');
        }

        // Get all lessons in order
        $allLessons = collect();
        foreach ($course->modules as $mod) {
            foreach ($mod->lessons as $les) {
                $allLessons->push($les);
            }
        }

        // Determine active lesson
        if ($lessonSlug) {
            $currentLesson = $allLessons->firstWhere('slug', $lessonSlug);
        } else {
            // Pick first uncompleted lesson, or very first lesson
            $completedLessonIds = LessonCompletion::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->pluck('lesson_id')
                ->toArray();

            $currentLesson = $allLessons->first(function ($l) use ($completedLessonIds) {
                return !in_array($l->id, $completedLessonIds);
            }) ?: $allLessons->first();
        }

        if (!$currentLesson && $allLessons->isNotEmpty()) {
            $currentLesson = $allLessons->first();
        }

        // Previous and Next lessons
        $currentIndex = $allLessons->search(function ($l) use ($currentLesson) {
            return $currentLesson && $l->id === $currentLesson->id;
        });

        $prevLesson = ($currentIndex !== false && $currentIndex > 0) ? $allLessons->get($currentIndex - 1) : null;
        $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1) ? $allLessons->get($currentIndex + 1) : null;

        // Completed lesson IDs for checkmarks
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->pluck('lesson_id')
            ->toArray();

        // Discussions for this lesson
        $discussions = $currentLesson ? $currentLesson->discussions()->with(['user', 'replies.user'])->get() : collect();

        return view('student.learn', compact(
            'course',
            'currentLesson',
            'prevLesson',
            'nextLesson',
            'completedLessonIds',
            'enrollment',
            'discussions'
        ));
    }

    /**
     * Mark lesson as completed or toggle completion.
     */
    public function toggleLessonCompletion(Request $request, Lesson $lesson)
    {
        $user = Auth::user();
        $course = $lesson->module->course;

        // Verify enrollment or instructor/admin authorization
        $isEnrolled = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists();
        if (!$isEnrolled && !$user->isAdmin() && $course->instructor_id !== $user->id) {
            return back()->with('error', 'You must be enrolled in this course to track lesson completion.');
        }

        $completion = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($completion) {
            $completion->delete();
            $message = 'Lesson marked as uncompleted.';
        } else {
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'course_id' => $course->id,
                'completed_at' => now(),
            ]);
            $message = 'Lesson completed!';
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $progress = $enrollment->recalculateProgress();
            if ($progress >= 100.00) {
                return back()->with('success', 'Congratulations! You have completed 100% of the course! Your certificate has been issued.');
            }
        }

        return back()->with('success', $message);
    }

    /**
     * Take a Quiz.
     */
    public function takeQuiz(Quiz $quiz)
    {
        $quiz->load(['questions', 'course']);
        $user = Auth::user();

        if (!$quiz->is_active) {
            return back()->with('error', 'This assessment is currently inactive.');
        }

        // Verify student is enrolled or course author/admin
        $isEnrolled = Enrollment::where('user_id', $user->id)->where('course_id', $quiz->course_id)->exists();
        if (!$isEnrolled && !$user->isAdmin() && $quiz->course->instructor_id !== $user->id) {
            return redirect()->route('courses.detail', $quiz->course->slug)
                ->with('warning', 'Please enroll in the course to take this assessment.');
        }

        // Check existing attempts
        $attemptsCount = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->count();
        if ($quiz->max_attempts > 0 && $attemptsCount >= $quiz->max_attempts) {
            return back()->with('error', 'You have reached the maximum allowed attempts for this quiz.');
        }

        // Record start time in session
        session(["quiz_start_{$quiz->id}_{$user->id}" => now()]);

        if ($quiz->randomize_questions) {
            $quiz->setRelation('questions', $quiz->questions->shuffle());
        }

        return view('student.quizzes.take', compact('quiz', 'attemptsCount'));
    }

    /**
     * Submit Quiz and auto-calculate grade.
     */
    public function submitQuiz(Request $request, Quiz $quiz)
    {
        $user = Auth::user();

        if (!$quiz->is_active) {
            return back()->with('error', 'This assessment is currently inactive.');
        }

        // Verify enrollment or instructor/admin authorization
        $isEnrolled = Enrollment::where('user_id', $user->id)->where('course_id', $quiz->course_id)->exists();
        if (!$isEnrolled && !$user->isAdmin() && $quiz->course->instructor_id !== $user->id) {
            abort(403, 'You must be enrolled in this course to submit assessments.');
        }

        // Check attempt limit
        $attemptsCount = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->count();
        if ($quiz->max_attempts > 0 && $attemptsCount >= $quiz->max_attempts) {
            return redirect()->route('student.learn', $quiz->course->slug)
                ->with('error', 'You have reached the maximum allowed attempts for this quiz.');
        }

        $answers = $request->input('answers', []);
        $questions = $quiz->questions;
        $totalPoints = 0;
        $earnedPoints = 0;
        $submissionDetails = [];

        foreach ($questions as $q) {
            $totalPoints += $q->points;
            $submitted = $answers[$q->id] ?? null;
            $isCorrect = false;

            $correctAnswers = (array) $q->correct_answers;

            if ($q->question_type === 'multiple_choice') {
                $submittedArray = is_array($submitted) ? $submitted : [$submitted];
                sort($submittedArray);
                sort($correctAnswers);
                $isCorrect = ($submittedArray == $correctAnswers);
            } else {
                $isCorrect = (strtolower(trim((string)$submitted)) === strtolower(trim((string)($correctAnswers[0] ?? ''))));
            }

            if ($isCorrect) {
                $earnedPoints += $q->points;
            }

            $submissionDetails[$q->id] = [
                'submitted' => $submitted,
                'correct' => $correctAnswers,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $q->points : 0,
            ];
        }

        $scorePercentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $isPassed = ($scorePercentage >= $quiz->passing_score_percentage);

        // Fetch started_at from session or fallback
        $fallbackMinutes = min(15, $quiz->time_limit_minutes ?: 15);
        $startedAt = session()->pull("quiz_start_{$quiz->id}_{$user->id}", now()->subMinutes($fallbackMinutes));

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score_percentage' => $scorePercentage,
            'earned_points' => $earnedPoints,
            'total_points' => $totalPoints,
            'is_passed' => $isPassed,
            'answers_submitted' => $submissionDetails,
            'started_at' => $startedAt,
            'completed_at' => now(),
        ]);

        // If passed and linked to lesson, mark lesson completed
        if ($isPassed && $quiz->lesson_id) {
            LessonCompletion::firstOrCreate([
                'user_id' => $user->id,
                'lesson_id' => $quiz->lesson_id,
                'course_id' => $quiz->course_id,
            ]);

            $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $quiz->course_id)->first();
            if ($enrollment) {
                $enrollment->recalculateProgress();
            }
        }

        return redirect()->route('student.quiz.result', $attempt)
            ->with($isPassed ? 'success' : 'warning', $isPassed ? 'Congratulations! You passed the assessment!' : 'Assessment submitted. You did not meet the passing score.');
    }

    /**
     * Show Quiz Attempt Result.
     */
    public function quizResult(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $attempt->load(['quiz.questions', 'quiz.course']);

        return view('student.quizzes.result', compact('attempt'));
    }

    /**
     * Post a question or reply to lesson discussion.
     */
    public function postDiscussion(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:discussions,id',
        ]);

        $course = $lesson->module->course;
        $isInstructor = (Auth::id() === $course->instructor_id || Auth::user()->isAdmin());
        $isEnrolled = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->exists();

        if (!$isInstructor && !$isEnrolled) {
            return back()->with('error', 'You must be enrolled in this course to join discussions.');
        }

        Discussion::create([
            'lesson_id' => $lesson->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment'],
            'is_instructor_reply' => $isInstructor,
        ]);

        return back()->with('success', 'Your comment has been posted.');
    }

    /**
     * Submit Course Rating & Review.
     */
    public function submitReview(Request $request, Course $course)
    {
        $user = Auth::user();

        // Ensure user is enrolled or administrator
        $isEnrolled = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists();
        if (!$isEnrolled && !$user->isAdmin()) {
            return back()->with('error', 'Only enrolled learners may submit course reviews.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        CourseReview::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? null,
                'is_approved' => true,
            ]
        );

        return back()->with('success', 'Thank you for your feedback! Your review has been published.');
    }

    /**
     * View all earned certificates.
     */
    public function certificates()
    {
        $certificates = Auth::user()->certificates()
            ->with(['course.instructor'])
            ->latest()
            ->paginate(10);

        return view('student.certificates.index', compact('certificates'));
    }
}
