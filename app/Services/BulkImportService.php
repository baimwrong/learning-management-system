<?php

namespace App\Services;

use App\Models\BulkImport;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BulkImportService
{
    /**
     * Process an uploaded CSV file based on import type.
     */
    public function processImport(UploadedFile $file, string $type, User $admin): BulkImport
    {
        $originalFilename = $file->getClientOriginalName();
        $storedPath = $file->storeAs('imports', uniqid('import_') . '_' . $originalFilename);

        $bulkImport = BulkImport::create([
            'user_id' => $admin->id,
            'import_type' => $type,
            'filename' => $storedPath,
            'original_filename' => $originalFilename,
            'status' => 'processing',
        ]);

        $fullPath = Storage::disk('local')->path($storedPath);
        $rows = $this->readCsv($fullPath);

        $totalRows = count($rows);
        $bulkImport->total_rows = $totalRows;
        $bulkImport->save();

        if ($totalRows === 0) {
            $bulkImport->update([
                'status' => 'failed',
                'error_log' => [['row' => 0, 'error' => 'The uploaded CSV file is empty or missing headers.']],
            ]);
            return $bulkImport;
        }

        $successful = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Accounting for 1-based index and header line

            try {
                switch ($type) {
                    case 'users':
                        $this->importUserRow($row, $rowNumber);
                        break;
                    case 'courses':
                        $this->importCourseRow($row, $rowNumber, $admin);
                        break;
                    case 'questions':
                        $this->importQuestionRow($row, $rowNumber);
                        break;
                    case 'enrollments':
                        $this->importEnrollmentRow($row, $rowNumber);
                        break;
                    default:
                        throw new \InvalidArgumentException("Unsupported import type: {$type}");
                }
                $successful++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                    'data' => $row,
                ];
            }

            $bulkImport->processed_rows = $successful + $failed;
            $bulkImport->successful_rows = $successful;
            $bulkImport->failed_rows = $failed;
        }

        $bulkImport->status = ($failed === 0) ? 'completed' : ($successful > 0 ? 'completed' : 'failed');
        $bulkImport->error_log = $errors;
        $bulkImport->summary = [
            'total' => $totalRows,
            'success' => $successful,
            'failed' => $failed,
            'success_rate' => $totalRows > 0 ? round(($successful / $totalRows) * 100, 2) . '%' : '0%',
        ];
        $bulkImport->save();

        return $bulkImport;
    }

    /**
     * Import a single user row.
     */
    protected function importUserRow(array $row, int $rowNumber): void
    {
        $validator = Validator::make($row, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'nullable|in:admin,instructor,student',
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        User::create([
            'name' => trim($row['name']),
            'email' => strtolower(trim($row['email'])),
            'role' => trim($row['role'] ?? 'student') ?: 'student',
            'password' => Hash::make(trim($row['password'] ?? 'Password123!')),
            'phone' => trim($row['phone'] ?? ''),
            'bio' => trim($row['bio'] ?? ''),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Import a single course row.
     */
    protected function importCourseRow(array $row, int $rowNumber, User $admin): void
    {
        $validator = Validator::make($row, [
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'instructor_email' => 'nullable|email',
            'level' => 'nullable|in:all_levels,beginner,intermediate,advanced',
            'price' => 'nullable|numeric|min:0',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        // Category resolution
        $categoryId = null;
        if (!empty($row['category'])) {
            $cat = Category::firstOrCreate(
                ['slug' => Str::slug($row['category'])],
                [
                    'name' => trim($row['category']),
                    'description' => 'Automatically created via bulk import',
                    'icon' => 'bi-tag',
                    'color' => '#0d6efd',
                    'is_active' => true,
                ]
            );
            $categoryId = $cat->id;
        }

        // Instructor resolution
        $instructorId = $admin->id;
        if (!empty($row['instructor_email'])) {
            $instructor = User::where('email', strtolower(trim($row['instructor_email'])))->first();
            if ($instructor) {
                $instructorId = $instructor->id;
            }
        }

        $baseSlug = Str::slug($row['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        Course::create([
            'title' => trim($row['title']),
            'slug' => $slug,
            'category_id' => $categoryId,
            'instructor_id' => $instructorId,
            'level' => trim($row['level'] ?? 'all_levels') ?: 'all_levels',
            'price' => (float) ($row['price'] ?? 0),
            'summary' => trim($row['summary'] ?? ''),
            'description' => trim($row['description'] ?? ''),
            'is_published' => filter_var($row['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * Import a quiz question row.
     */
    protected function importQuestionRow(array $row, int $rowNumber): void
    {
        $validator = Validator::make($row, [
            'course' => 'required|string', // Course slug or ID
            'quiz_title' => 'required|string|max:255',
            'question_text' => 'required|string',
            'question_type' => 'nullable|in:single_choice,multiple_choice,true_false,short_answer',
            'options' => 'nullable|string', // Pipe-delimited or JSON string
            'correct_answer' => 'required|string',
            'points' => 'nullable|integer|min:1',
            'explanation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        // Find course
        $course = Course::where('slug', trim($row['course']))
            ->orWhere('id', is_numeric($row['course']) ? (int) $row['course'] : 0)
            ->first();

        if (!$course) {
            throw new \Exception("Course '{$row['course']}' not found.");
        }

        // Find or create quiz
        $quiz = Quiz::firstOrCreate(
            [
                'course_id' => $course->id,
                'title' => trim($row['quiz_title']),
            ],
            [
                'description' => 'Created via bulk question import',
                'time_limit_minutes' => 30,
                'passing_score_percentage' => 70,
                'max_attempts' => 3,
                'is_active' => true,
            ]
        );

        // Parse options
        $optionsRaw = trim($row['options'] ?? '');
        $optionsArray = [];
        if (!empty($optionsRaw)) {
            $decoded = json_decode($optionsRaw, true);
            if (is_array($decoded)) {
                $optionsArray = $decoded;
            } else {
                // Pipe separated: "HyperText Markup Language|Home Tool Markup Language|Hyperlinks Text"
                $parts = explode('|', $optionsRaw);
                $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                foreach ($parts as $pIndex => $part) {
                    $key = $letters[$pIndex] ?? (string) ($pIndex + 1);
                    $optionsArray[] = [
                        'key' => $key,
                        'text' => trim($part),
                    ];
                }
            }
        }

        // Correct answer parsing
        $correctAnswer = trim($row['correct_answer']);
        $correctArray = explode(',', $correctAnswer);
        $correctArray = array_map('trim', $correctArray);

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => trim($row['question_text']),
            'question_type' => trim($row['question_type'] ?? 'single_choice') ?: 'single_choice',
            'options' => $optionsArray,
            'correct_answers' => $correctArray,
            'points' => (int) ($row['points'] ?? 1) ?: 1,
            'explanation' => trim($row['explanation'] ?? ''),
            'order_number' => $quiz->questions()->count() + 1,
        ]);
    }

    /**
     * Import an enrollment row.
     */
    protected function importEnrollmentRow(array $row, int $rowNumber): void
    {
        $validator = Validator::make($row, [
            'student_email' => 'required|email',
            'course' => 'required|string', // Course slug or ID
            'status' => 'nullable|in:active,completed,dropped',
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        $user = User::where('email', strtolower(trim($row['student_email'])))->first();
        if (!$user) {
            throw new \Exception("Student with email '{$row['student_email']}' not found.");
        }

        $course = Course::where('slug', trim($row['course']))
            ->orWhere('id', is_numeric($row['course']) ? (int) $row['course'] : 0)
            ->first();

        if (!$course) {
            throw new \Exception("Course '{$row['course']}' not found.");
        }

        Enrollment::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'status' => trim($row['status'] ?? 'active') ?: 'active',
                'enrolled_at' => now(),
            ]
        );
    }

    /**
     * Read CSV into an associative array with headers as keys.
     */
    public function readCsv(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            // Read header row
            $headers = fgetcsv($handle, 10000, ',');
            if (!$headers) {
                fclose($handle);
                return [];
            }

            // Clean header strings (remove BOM and trim)
            $headers = array_map(function ($h) {
                return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
            }, $headers);

            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                if (count($data) === 1 && empty($data[0])) {
                    continue; // Skip empty lines
                }

                $row = [];
                foreach ($headers as $i => $header) {
                    $row[$header] = $data[$i] ?? '';
                }
                $rows[] = $row;
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Generate sample CSV template for download.
     */
    public function getCsvTemplate(string $type): string
    {
        switch ($type) {
            case 'users':
                return "name,email,role,password,phone,bio\n" .
                    "Alice Walker,alice@example.com,student,Password123!,+1234567890,Software engineering student\n" .
                    "Dr. Robert Ford,ford@example.com,instructor,Password123!,+1987654321,Senior AI and Distributed Systems Researcher\n" .
                    "David Miller,david@example.com,student,Password123!,+1555234567,Full stack web enthusiast\n";

            case 'courses':
                return "title,category,instructor_email,level,price,summary,description,is_published\n" .
                    "Advanced PostgreSQL 18 & Database Engineering,Database,ford@example.com,advanced,79.99,Master indexes partitioning and JSONB in PG 18,Comprehensive deep-dive into PostgreSQL 18 enterprise architecture,1\n" .
                    "Modern Web Development with Laravel 13,Web Development,ford@example.com,intermediate,49.99,Build secure production-grade apps with Laravel 13,Hands-on Laravel 13 curriculum with Eloquent and Blade,1\n";

            case 'questions':
                return "course,quiz_title,question_text,question_type,options,correct_answer,points,explanation\n" .
                    "advanced-postgresql-18-database-engineering,PostgreSQL 18 Mastery Exam,Which index type is best suited for JSONB containment (@>) queries in PostgreSQL 18?,single_choice,B-Tree|GIN Index|GiST Index|Hash Index,B,2,GIN (Generalized Inverted Index) is optimized for JSONB containment and path operations.\n" .
                    "modern-web-development-with-laravel-13,Laravel 13 Architecture Assessment,What is the minimum PHP version required by Laravel 13?,single_choice,PHP 8.1|PHP 8.2|PHP 8.3|PHP 8.0,C,1,Laravel 13 requires PHP 8.3 or higher.\n";

            case 'enrollments':
                return "student_email,course,status\n" .
                    "alice@example.com,advanced-postgresql-18-database-engineering,active\n" .
                    "david@example.com,modern-web-development-with-laravel-13,active\n";

            default:
                return "";
        }
    }
}
