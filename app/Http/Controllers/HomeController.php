<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the LMS landing page.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)->withCount('courses')->get();
        $featuredCourses = Course::where('is_published', true)
            ->where('is_featured', true)
            ->with(['category', 'instructor', 'reviews'])
            ->latest()
            ->take(6)
            ->get();

        $latestCourses = Course::where('is_published', true)
            ->with(['category', 'instructor', 'reviews'])
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_courses' => Course::where('is_published', true)->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_enrollments' => Enrollment::count(),
        ];

        return view('pages.home', compact('categories', 'featuredCourses', 'latestCourses', 'stats'));
    }

    /**
     * Browse course catalog with search and filters.
     */
    public function courses(Request $request)
    {
        $query = Course::where('is_published', true)->with(['category', 'instructor', 'reviews']);

        // Search by keyword
        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'ilike', $keyword)
                    ->orWhere('summary', 'ilike', $keyword)
                    ->orWhere('description', 'ilike', $keyword);
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Level filter
        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level', $request->level);
        }

        // Price filter (free vs paid)
        if ($request->filled('price_type')) {
            if ($request->price_type === 'free') {
                $query->where('price', '<=', 0);
            } elseif ($request->price_type === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        // Sort options
        switch ($request->get('sort', 'latest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $courses = $query->paginate(9)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('courses')->get();

        return view('pages.courses', compact('courses', 'categories'));
    }

    /**
     * View single course detail page.
     */
    public function courseDetail(string $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_published', true)
            ->with([
                'category',
                'instructor',
                'modules.lessons',
                'reviews.user',
            ])
            ->firstOrFail();

        $isEnrolled = false;
        $enrollment = null;

        if (Auth::check()) {
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();
            $isEnrolled = (bool) $enrollment;
        }

        $relatedCourses = Course::where('is_published', true)
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->take(3)
            ->get();

        return view('pages.course-detail', compact('course', 'isEnrolled', 'enrollment', 'relatedCourses'));
    }

    /**
     * Browse courses in a specific category.
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $courses = Course::where('category_id', $category->id)
            ->where('is_published', true)
            ->with(['instructor', 'reviews'])
            ->latest()
            ->paginate(9);

        return view('pages.category', compact('category', 'courses'));
    }
}
