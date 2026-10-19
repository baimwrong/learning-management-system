<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress_percentage',
        'enrolled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Recalculate progress percentage based on lesson completions.
     */
    public function recalculateProgress(): float
    {
        $course = $this->course;
        $totalLessons = $course->lessons()->count();

        if ($totalLessons === 0) {
            $this->progress_percentage = 0.00;
            $this->status = 'active';
            $this->completed_at = null;
            $this->save();
            return 0.00;
        }

        $completedCount = LessonCompletion::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->count();

        $percentage = min(100.00, round(($completedCount / $totalLessons) * 100, 2));
        $this->progress_percentage = $percentage;

        if ($percentage >= 100.00) {
            $this->status = 'completed';
            if (!$this->completed_at) {
                $this->completed_at = now();
            }

            // Award certificate if not already issued
            Certificate::firstOrCreate(
                [
                    'user_id' => $this->user_id,
                    'course_id' => $this->course_id,
                ],
                [
                    'certificate_code' => 'LMS-' . strtoupper(Str::random(10)),
                    'issued_at' => now(),
                    'metadata' => [
                        'course_title' => $course->title,
                        'student_name' => $this->user->name,
                        'instructor_name' => $course->instructor?->name ?? 'Course Faculty',
                    ],
                ]
            );
        } else {
            $this->status = 'active';
            $this->completed_at = null;
        }

        $this->save();
        return $percentage;
    }
}
