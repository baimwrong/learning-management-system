<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseReview;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive LMS dataset.
     */
    public function run(): void
    {
        // 1. Create Default Administrative & Core Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+1 (555) 019-2831',
                'bio' => 'Lead System Architect & System Administrator.',
                'email_verified_at' => now(),
            ]
        );

        $instructor1 = User::firstOrCreate(
            ['email' => 'instructor@lms.local'],
            [
                'name' => 'Prof. Marcus Vance',
                'password' => Hash::make('Instructor123!'),
                'role' => 'instructor',
                'status' => 'active',
                'phone' => '+1 (555) 014-9922',
                'bio' => 'Principal Software Architect & Full-Stack Specialist with 15+ years experience in distributed cloud architectures.',
                'email_verified_at' => now(),
            ]
        );

        $instructor2 = User::firstOrCreate(
            ['email' => 'ford@lms.local'],
            [
                'name' => 'Dr. Robert Ford',
                'password' => Hash::make('Instructor123!'),
                'role' => 'instructor',
                'status' => 'active',
                'phone' => '+1 (555) 017-3849',
                'bio' => 'Database Performance Engineer & Systems Researcher specializing in PostgreSQL internals and indexing.',
                'email_verified_at' => now(),
            ]
        );

        $student1 = User::firstOrCreate(
            ['email' => 'student@lms.local'],
            [
                'name' => 'Alex Morgan',
                'password' => Hash::make('Student123!'),
                'role' => 'student',
                'status' => 'active',
                'phone' => '+1 (555) 012-3456',
                'bio' => 'Passionate computer science student pursuing enterprise web engineering.',
                'email_verified_at' => now(),
            ]
        );

        $student2 = User::firstOrCreate(
            ['email' => 'alice@lms.local'],
            [
                'name' => 'Alice Walker',
                'password' => Hash::make('Student123!'),
                'role' => 'student',
                'status' => 'active',
                'phone' => '+1 (555) 018-7744',
                'bio' => 'Data analyst transitioning to backend database design.',
                'email_verified_at' => now(),
            ]
        );

        $student3 = User::firstOrCreate(
            ['email' => 'david@lms.local'],
            [
                'name' => 'David Miller',
                'password' => Hash::make('Student123!'),
                'role' => 'student',
                'status' => 'active',
                'phone' => '+1 (555) 015-8833',
                'bio' => 'Junior developer building scalable microservices.',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Core LMS Categories
        $categoriesData = [
            [
                'name' => 'Database Engineering',
                'slug' => 'database-engineering',
                'description' => 'Master PostgreSQL, indexing, query optimization, high-availability, and relational data architecture.',
                'icon' => 'bi-database-fill-gear',
                'color' => '#0d6efd',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Modern PHP, Laravel 13, Vue, React, Bootstrap, and API microservice design.',
                'icon' => 'bi-code-slash',
                'color' => '#6610f2',
            ],
            [
                'name' => 'Cloud & DevOps',
                'slug' => 'cloud-devops',
                'description' => 'Docker, Kubernetes, CI/CD automated pipelines, Linux administration, and infrastructure as code.',
                'icon' => 'bi-cloud-check-fill',
                'color' => '#0dcaf0',
            ],
            [
                'name' => 'Cybersecurity',
                'slug' => 'cybersecurity',
                'description' => 'Defensive programming, application security, authentication protocols, and penetration testing.',
                'icon' => 'bi-shield-lock-fill',
                'color' => '#dc3545',
            ],
            [
                'name' => 'Artificial Intelligence',
                'slug' => 'artificial-intelligence',
                'description' => 'Machine learning pipelines, LLM prompt engineering, and intelligent agent integrations.',
                'icon' => 'bi-cpu-fill',
                'color' => '#fd7e14',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Create Flagship Courses
        // Course 1: PostgreSQL 18
        $course1 = Course::firstOrCreate(
            ['slug' => 'mastering-postgresql-18-architecture-and-tuning'],
            [
                'title' => 'Mastering PostgreSQL 18: Architecture & Query Optimization',
                'category_id' => $categories['database-engineering']->id,
                'instructor_id' => $instructor2->id,
                'summary' => 'Deep dive into the latest PostgreSQL 18 engine, advanced indexing, JSONB performance, and high-throughput connection tuning.',
                'description' => "PostgreSQL 18 introduces groundbreaking advancements in query execution, memory management, parallel sequencing, and native JSON acceleration.\n\nThis comprehensive masterclass takes you from architectural fundamentals to advanced database performance engineering. You will learn how to design schemas for high concurrency, inspect explain plans with surgical precision, construct GIN indexes for JSONB workloads, and configure enterprise-grade replication.",
                'level' => 'advanced',
                'price' => 89.99,
                'is_published' => true,
                'is_featured' => true,
                'requirements' => [
                    'Fundamental knowledge of SQL (SELECT, INSERT, UPDATE, JOIN)',
                    'Basic familiarity with terminal or CLI environments',
                    'A local or remote PostgreSQL instance',
                ],
                'learning_outcomes' => [
                    'Master PostgreSQL 18 core storage engines, WAL mechanism, and vacuum tuning',
                    'Build high-performance JSONB document queries with GIN indexing',
                    'Interpret complex EXPLAIN (ANALYZE, BUFFERS) query execution plans',
                    'Design partitioned tables and zero-downtime database migrations',
                    'Implement row-level security (RLS) and enterprise access policies',
                ],
            ]
        );

        // Course 2: Laravel 13
        $course2 = Course::firstOrCreate(
            ['slug' => 'enterprise-application-development-with-laravel-13'],
            [
                'title' => 'Enterprise Application Development with Laravel 13',
                'category_id' => $categories['web-development']->id,
                'instructor_id' => $instructor1->id,
                'summary' => 'Build robust, modular, and high-performance full-stack web applications using Laravel 13 and modern design patterns.',
                'description' => "Laravel 13 continues the evolution of the world's most productive web framework. In this course, you will build production-grade web systems from scratch using service layers, repository patterns, custom artisan commands, queue workers, and offline-ready asset pipelines.",
                'level' => 'intermediate',
                'price' => 69.99,
                'is_published' => true,
                'is_featured' => true,
                'requirements' => [
                    'Proficiency in PHP 8.2 or higher (OOP principles, attributes, type system)',
                    'Understanding of MVC (Model-View-Controller) architecture',
                ],
                'learning_outcomes' => [
                    'Master Laravel 13 dependency injection and service containers',
                    'Build multi-role authentication with middleware guards',
                    'Construct asynchronous background queues and job dispatchers',
                    'Implement high-speed bulk CSV processing and JSON logging',
                ],
            ]
        );

        // Course 3: Modern UI with Bootstrap 5
        $course3 = Course::firstOrCreate(
            ['slug' => 'modern-responsive-ui-with-bootstrap-5'],
            [
                'title' => 'Modern Responsive UI Design with Bootstrap 5.3',
                'category_id' => $categories['web-development']->id,
                'instructor_id' => $instructor1->id,
                'summary' => 'Design clean, accessible, dark-mode ready web interfaces without relying on third-party cloud CDN dependencies.',
                'description' => "Learn to build sleek, enterprise-ready user interfaces using Bootstrap 5.3's CSS variables, utility-first classes, and modular icon libraries. Perfect for internal network deployments and air-gapped intranet environments.",
                'level' => 'beginner',
                'price' => 0.00, // Free course
                'is_published' => true,
                'is_featured' => false,
                'requirements' => [
                    'Basic HTML5 and CSS3 knowledge',
                ],
                'learning_outcomes' => [
                    'Master Bootstrap 5 flexbox and grid layouts',
                    'Implement dark and light mode color themes with CSS custom properties',
                    'Bundle offline icon libraries (Bootstrap Icons and FontAwesome)',
                ],
            ]
        );

        // 4. Create Modules and Lessons for Course 1 (PostgreSQL 18)
        $mod1 = CourseModule::firstOrCreate(
            ['course_id' => $course1->id, 'title' => 'Module 1: PostgreSQL 18 Core Architecture'],
            ['description' => 'Internal storage, process models, and buffer pools.', 'order_number' => 1]
        );

        $les1 = Lesson::firstOrCreate(
            ['module_id' => $mod1->id, 'slug' => 'pg18-architecture-overview'],
            [
                'title' => 'PostgreSQL 18 Process & Memory Architecture',
                'content_type' => 'reading',
                'duration_minutes' => 15,
                'order_number' => 1,
                'is_preview' => true,
                'content' => "<h3>Understanding PostgreSQL 18 Internals</h3><p>PostgreSQL operates under a process-based model rather than a multithreaded model. When a client establishes a connection, the postmaster master process forks a dedicated backend worker process.</p><h4>Key Memory Areas:</h4><ul><li><strong>shared_buffers:</strong> The primary shared cache where dirty and clean data pages reside.</li><li><strong>work_mem:</strong> Memory allocated per sorting operation and hash table within queries.</li><li><strong>maintenance_work_mem:</strong> Dedicated memory for VACUUM, CREATE INDEX, and foreign key additions.</li><li><strong>wal_buffers:</strong> Buffer holding write-ahead log data before flushing to persistent disk.</li></ul>",
            ]
        );

        $les2 = Lesson::firstOrCreate(
            ['module_id' => $mod1->id, 'slug' => 'pg18-jsonb-enhancements'],
            [
                'title' => 'Advanced JSONB Indexing & Query Acceleration',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 22,
                'order_number' => 2,
                'is_preview' => false,
                'content' => "<p>In this lesson, we explore how PostgreSQL 18 processes nested JSONB data with optimized GIN (Generalized Inverted Index) path operators.</p><pre><code>CREATE INDEX idx_users_metadata_gin ON users USING GIN (metadata jsonb_path_ops);</code></pre>",
            ]
        );

        $mod2 = CourseModule::firstOrCreate(
            ['course_id' => $course1->id, 'title' => 'Module 2: Assessments & Hands-On Evaluation'],
            ['description' => 'Test your database mastery with the comprehensive assessment.', 'order_number' => 2]
        );

        $les3 = Lesson::firstOrCreate(
            ['module_id' => $mod2->id, 'slug' => 'pg18-mastery-quiz-lesson'],
            [
                'title' => 'PostgreSQL 18 Certification Assessment',
                'content_type' => 'quiz',
                'duration_minutes' => 20,
                'order_number' => 1,
                'is_preview' => false,
                'content' => '<p>Demonstrate your proficiency in PostgreSQL 18 internals to complete the module and qualify for certification.</p>',
            ]
        );

        // Create Quiz for Lesson 3
        $quiz1 = Quiz::firstOrCreate(
            ['course_id' => $course1->id, 'lesson_id' => $les3->id],
            [
                'title' => 'PostgreSQL 18 Architecture Mastery Exam',
                'description' => 'Evaluate your knowledge on shared_buffers, GIN indexing, and query planning.',
                'time_limit_minutes' => 25,
                'passing_score_percentage' => 70,
                'max_attempts' => 5,
                'is_active' => true,
            ]
        );

        // Questions for Quiz 1
        QuizQuestion::firstOrCreate(
            ['quiz_id' => $quiz1->id, 'order_number' => 1],
            [
                'question_text' => 'Which memory configuration parameter directly controls the shared buffer cache in PostgreSQL?',
                'question_type' => 'single_choice',
                'options' => [
                    ['key' => 'A', 'text' => 'work_mem'],
                    ['key' => 'B', 'text' => 'shared_buffers'],
                    ['key' => 'C', 'text' => 'maintenance_work_mem'],
                    ['key' => 'D', 'text' => 'effective_cache_size'],
                ],
                'correct_answers' => ['B'],
                'points' => 2,
                'explanation' => 'shared_buffers specifies how much memory is dedicated to PostgreSQL to use for caching data pages.',
            ]
        );

        QuizQuestion::firstOrCreate(
            ['quiz_id' => $quiz1->id, 'order_number' => 2],
            [
                'question_text' => 'Which index type is specifically optimized for JSONB containment (@>) queries in PostgreSQL?',
                'question_type' => 'single_choice',
                'options' => [
                    ['key' => 'A', 'text' => 'B-Tree'],
                    ['key' => 'B', 'text' => 'Hash Index'],
                    ['key' => 'C', 'text' => 'GIN (Generalized Inverted Index)'],
                    ['key' => 'D', 'text' => 'BRIN (Block Range Index)'],
                ],
                'correct_answers' => ['C'],
                'points' => 2,
                'explanation' => 'GIN indexes allow indexing composite items like arrays and JSONB keys/values, accelerating @> containment queries.',
            ]
        );

        QuizQuestion::firstOrCreate(
            ['quiz_id' => $quiz1->id, 'order_number' => 3],
            [
                'question_text' => 'True or False: PostgreSQL uses multithreading for each incoming client connection instead of a dedicated process fork.',
                'question_type' => 'true_false',
                'options' => [
                    ['key' => 'A', 'text' => 'True'],
                    ['key' => 'B', 'text' => 'False'],
                ],
                'correct_answers' => ['B'],
                'points' => 1,
                'explanation' => 'False: PostgreSQL is a process-based database engine where the postmaster process forks a new worker process per client.',
            ]
        );

        // Modules & Lessons for Course 2 (Laravel 13)
        $modL1 = CourseModule::firstOrCreate(
            ['course_id' => $course2->id, 'title' => 'Module 1: The Modern Laravel 13 Foundation'],
            ['description' => 'Explore the streamlined bootstrap and route architecture.', 'order_number' => 1]
        );

        Lesson::firstOrCreate(
            ['module_id' => $modL1->id, 'slug' => 'laravel-13-bootstrap-architecture'],
            [
                'title' => 'Laravel 13 Streamlined Application Bootstrap',
                'content_type' => 'reading',
                'duration_minutes' => 18,
                'order_number' => 1,
                'is_preview' => true,
                'content' => "<h3>Modern Application Configuration</h3><p>Laravel 13 continues to unify configuration, routing, and middleware in <code>bootstrap/app.php</code>. This simplifies middleware aliasing, exception handling, and health inspection endpoints.</p>",
            ]
        );

        Lesson::firstOrCreate(
            ['module_id' => $modL1->id, 'slug' => 'bulk-data-processing-and-csv-streaming'],
            [
                'title' => 'High-Performance Bulk Import & Data Streaming',
                'content_type' => 'reading',
                'duration_minutes' => 25,
                'order_number' => 2,
                'is_preview' => false,
                'content' => "<p>Learn how to safely ingest thousands of CSV records using Laravel database transactions and PostgreSQL JSONB error tracing.</p>",
            ]
        );

        // 5. Seed Enrollments & Completed Student Progress
        $enrollment1 = Enrollment::firstOrCreate(
            ['user_id' => $student1->id, 'course_id' => $course1->id],
            [
                'status' => 'completed',
                'progress_percentage' => 100.00,
                'enrolled_at' => now()->subDays(10),
                'completed_at' => now()->subDays(1),
            ]
        );

        // Mark lessons completed for Alex
        LessonCompletion::firstOrCreate(
            ['user_id' => $student1->id, 'lesson_id' => $les1->id],
            ['course_id' => $course1->id, 'completed_at' => now()->subDays(8)]
        );
        LessonCompletion::firstOrCreate(
            ['user_id' => $student1->id, 'lesson_id' => $les2->id],
            ['course_id' => $course1->id, 'completed_at' => now()->subDays(5)]
        );
        LessonCompletion::firstOrCreate(
            ['user_id' => $student1->id, 'lesson_id' => $les3->id],
            ['course_id' => $course1->id, 'completed_at' => now()->subDays(1)]
        );

        // Quiz attempt for Alex
        QuizAttempt::firstOrCreate(
            ['user_id' => $student1->id, 'quiz_id' => $quiz1->id],
            [
                'score_percentage' => 100.00,
                'earned_points' => 5,
                'total_points' => 5,
                'is_passed' => true,
                'started_at' => now()->subDays(1)->subMinutes(20),
                'completed_at' => now()->subDays(1),
                'answers_submitted' => [
                    1 => ['submitted' => 'B', 'correct' => ['B'], 'is_correct' => true, 'points_earned' => 2],
                    2 => ['submitted' => 'C', 'correct' => ['C'], 'is_correct' => true, 'points_earned' => 2],
                    3 => ['submitted' => 'B', 'correct' => ['B'], 'is_correct' => true, 'points_earned' => 1],
                ],
            ]
        );

        // Issue Certificate for Alex
        Certificate::firstOrCreate(
            ['user_id' => $student1->id, 'course_id' => $course1->id],
            [
                'certificate_code' => 'LMS-2026-PG18-9842',
                'issued_at' => now()->subDays(1),
                'metadata' => [
                    'course_title' => $course1->title,
                    'student_name' => $student1->name,
                    'instructor_name' => $instructor2->name,
                    'score' => '100%',
                ],
            ]
        );

        // Enroll Alice in Laravel 13
        Enrollment::firstOrCreate(
            ['user_id' => $student2->id, 'course_id' => $course2->id],
            [
                'status' => 'active',
                'progress_percentage' => 50.00,
                'enrolled_at' => now()->subDays(4),
            ]
        );

        // Enroll David in Bootstrap 5
        Enrollment::firstOrCreate(
            ['user_id' => $student3->id, 'course_id' => $course3->id],
            [
                'status' => 'active',
                'progress_percentage' => 0.00,
                'enrolled_at' => now()->subDays(2),
            ]
        );

        // 6. Seed Course Reviews
        CourseReview::firstOrCreate(
            ['user_id' => $student1->id, 'course_id' => $course1->id],
            [
                'rating' => 5,
                'review' => 'Exceptional course! The explanations of PostgreSQL 18 memory structures and GIN indexing on JSONB fields were crystal clear.',
                'is_approved' => true,
            ]
        );

        CourseReview::firstOrCreate(
            ['user_id' => $student2->id, 'course_id' => $course1->id],
            [
                'rating' => 5,
                'review' => 'Dr. Ford provides top-tier database engineering content. Highly recommended for any senior backend developer.',
                'is_approved' => true,
            ]
        );

        // 7. Seed Q&A Discussions
        $disc1 = Discussion::firstOrCreate(
            ['lesson_id' => $les1->id, 'user_id' => $student1->id],
            [
                'comment' => 'When setting shared_buffers on a 32GB RAM server, is 25% (8GB) still the recommended general rule for PostgreSQL 18?',
                'is_instructor_reply' => false,
                'is_pinned' => true,
            ]
        );

        Discussion::firstOrCreate(
            ['lesson_id' => $les1->id, 'parent_id' => $disc1->id, 'user_id' => $instructor2->id],
            [
                'comment' => 'Yes, 25% (8GB) remains the sweet spot for shared_buffers because PostgreSQL relies heavily on the operating system kernel page cache for read operations.',
                'is_instructor_reply' => true,
                'is_pinned' => false,
            ]
        );
    }
}
