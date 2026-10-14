<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BulkImportController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [HomeController::class, 'courses'])->name('courses.index');
Route::get('/courses/{slug}', [HomeController::class, 'courseDetail'])->name('courses.detail');
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('categories.show');

// Certificate public verification & view
Route::get('/verify-certificate/{code?}', [CertificateController::class, 'verify'])->name('certificates.verify');
Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/auth/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('auth.quick-login');

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student,admin,instructor'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');
    Route::post('/enroll/{course}', [StudentController::class, 'enroll'])->name('enroll');
    Route::get('/learn/{courseSlug}/{lessonSlug?}', [StudentController::class, 'learn'])->name('learn');
    Route::post('/lessons/{lesson}/complete', [StudentController::class, 'toggleLessonCompletion'])->name('lessons.complete');
    
    // Quizzes & Assessments
    Route::get('/quizzes/{quiz}/take', [StudentController::class, 'takeQuiz'])->name('quiz.take');
    Route::post('/quizzes/{quiz}/submit', [StudentController::class, 'submitQuiz'])->name('quiz.submit');
    Route::get('/quiz-attempts/{attempt}', [StudentController::class, 'quizResult'])->name('quiz.result');
    
    // Lesson Discussions & Course Reviews
    Route::post('/lessons/{lesson}/discussions', [StudentController::class, 'postDiscussion'])->name('discussions.store');
    Route::post('/courses/{course}/review', [StudentController::class, 'submitReview'])->name('reviews.store');
    
    // Certificates
    Route::get('/certificates', [StudentController::class, 'certificates'])->name('certificates');
});

/*
|--------------------------------------------------------------------------
| Instructor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:instructor,admin'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [InstructorController::class, 'courses'])->name('courses.index');
    Route::get('/courses/create', [InstructorController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [InstructorController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}/edit', [InstructorController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorController::class, 'updateCourse'])->name('courses.update');
    
    // Curriculum Builder (Modules & Lessons)
    Route::get('/courses/{course}/curriculum', [InstructorController::class, 'curriculum'])->name('courses.curriculum');
    Route::post('/courses/{course}/modules', [InstructorController::class, 'storeModule'])->name('modules.store');
    Route::delete('/modules/{module}', [InstructorController::class, 'deleteModule'])->name('modules.destroy');
    Route::post('/modules/{module}/lessons', [InstructorController::class, 'storeLesson'])->name('lessons.store');
    Route::delete('/lessons/{lesson}', [InstructorController::class, 'deleteLesson'])->name('lessons.destroy');
    
    // Quiz & Question Bank Builder
    Route::get('/quizzes/{quiz}/builder', [InstructorController::class, 'quizBuilder'])->name('quizzes.builder');
    Route::post('/quizzes/{quiz}/questions', [InstructorController::class, 'storeQuestion'])->name('questions.store');
    Route::delete('/questions/{question}', [InstructorController::class, 'deleteQuestion'])->name('questions.destroy');
    
    // Enrolled Students
    Route::get('/students', [InstructorController::class, 'students'])->name('students');
});

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    
    // Course Moderation
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses.index');
    Route::post('/courses/{course}/toggle-publish', [AdminController::class, 'toggleCoursePublish'])->name('courses.toggle-publish');
    Route::delete('/courses/{course}', [AdminController::class, 'deleteCourse'])->name('courses.destroy');
    
    // Categories Management
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');
    
    // Reviews Moderation
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::post('/reviews/{review}/toggle', [AdminController::class, 'toggleReviewApproval'])->name('reviews.toggle');
    Route::delete('/reviews/{review}', [AdminController::class, 'deleteReview'])->name('reviews.destroy');
    
    // System & PostgreSQL Status
    Route::get('/system', [AdminController::class, 'system'])->name('system');
    
    // Bulk Import Data Center
    Route::get('/bulk-import', [BulkImportController::class, 'index'])->name('bulk-import.index');
    Route::post('/bulk-import/upload', [BulkImportController::class, 'upload'])->name('bulk-import.upload');
    Route::get('/bulk-import/{bulkImport}', [BulkImportController::class, 'show'])->name('bulk-import.show');
    Route::get('/bulk-import/template/{type}', [BulkImportController::class, 'downloadTemplate'])->name('bulk-import.template');
    Route::delete('/bulk-import/{bulkImport}', [BulkImportController::class, 'destroy'])->name('bulk-import.destroy');
});
