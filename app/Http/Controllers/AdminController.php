<?php

namespace App\Http\Controllers;

use App\Models\BulkImport;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Admin Overview Dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('is_published', true)->count(),
            'total_enrollments' => Enrollment::count(),
            'active_enrollments' => Enrollment::where('status', 'active')->count(),
            'completed_enrollments' => Enrollment::where('status', 'completed')->count(),
            'total_quizzes' => Quiz::count(),
            'total_imports' => BulkImport::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentEnrollments = Enrollment::with(['user', 'course'])->latest()->take(5)->get();
        $recentCourses = Course::with(['instructor', 'category'])->latest()->take(5)->get();
        $recentImports = BulkImport::with('user')->latest()->take(4)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentEnrollments', 'recentCourses', 'recentImports'));
    }

    /**
     * User Management.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $likeOp = (DB::connection()->getDriverName() === 'pgsql') ? 'ilike' : 'like';
            $query->where(function ($q) use ($keyword, $likeOp) {
                $q->where('name', $likeOp, $keyword)
                    ->orWhere('email', $likeOp, $keyword);
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Store new user manually.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,instructor,student',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,suspended',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "User '{$validated['name']}' created successfully.");
    }

    /**
     * Update user details.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,instructor,student',
            'status' => 'required|in:active,suspended',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        $user->phone = $validated['phone'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Course Moderation & List.
     */
    public function courses(Request $request)
    {
        $query = Course::with(['category', 'instructor', 'enrollments']);

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $likeOp = (DB::connection()->getDriverName() === 'pgsql') ? 'ilike' : 'like';
            $query->where('title', $likeOp, $keyword);
        }

        $courses = $query->latest()->paginate(15)->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Toggle course publication status.
     */
    public function toggleCoursePublish(Course $course)
    {
        $course->is_published = !$course->is_published;
        $course->save();

        $status = $course->is_published ? 'published' : 'moved to draft';
        return back()->with('success', "Course '{$course->title}' has been {$status}.");
    }

    /**
     * Delete course.
     */
    public function deleteCourse(Course $course)
    {
        $course->delete();
        return back()->with('success', "Course deleted successfully.");
    }

    /**
     * Categories Management.
     */
    public function categories()
    {
        $categories = Category::withCount('courses')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store new Category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?: 'bi-folder',
            'color' => $validated['color'] ?: '#0d6efd',
            'is_active' => true,
        ]);

        return back()->with('success', "Category '{$validated['name']}' created successfully.");
    }

    /**
     * Update Category.
     */
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?: 'bi-folder',
            'color' => $validated['color'] ?: '#0d6efd',
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Category '{$category->name}' updated.");
    }

    /**
     * Delete Category.
     */
    public function deleteCategory(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Manage Course Reviews.
     */
    public function reviews()
    {
        $reviews = CourseReview::with(['course', 'user'])->latest()->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Toggle review approval.
     */
    public function toggleReviewApproval(CourseReview $review)
    {
        $review->is_approved = !$review->is_approved;
        $review->save();

        return back()->with('success', 'Review status updated.');
    }

    /**
     * Delete Review.
     */
    public function deleteReview(CourseReview $review)
    {
        $review->delete();
        return back()->with('success', 'Review removed.');
    }

    /**
     * System & PostgreSQL 18 Information.
     */
    public function system()
    {
        $pgVersion = 'PostgreSQL 18 Ready';
        try {
            $pdo = DB::connection()->getPdo();
            $pgVersion = $pdo->query('select version()')->fetchColumn();
        } catch (\Throwable $e) {
            $pgVersion = 'PostgreSQL connection pending setup (' . $e->getMessage() . ')';
        }

        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database_driver' => config('database.default'),
            'database_version' => $pgVersion,
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'Enabled' : 'Disabled',
            'storage_status' => is_writable(storage_path()) ? 'Writable' : 'Read-only',
        ];

        return view('admin.system', compact('systemInfo'));
    }
}
