<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'instructor_id',
        'summary',
        'description',
        'level',
        'price',
        'thumbnail',
        'is_published',
        'is_featured',
        'requirements',
        'learning_outcomes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'requirements' => 'array',
            'learning_outcomes' => 'array',
            'metadata' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order_number');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseModule::class, 'course_id', 'module_id')
            ->orderBy('order_number');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['id', 'status', 'progress_percentage', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Compute average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        return round((float) ($this->reviews()->where('is_approved', true)->avg('rating') ?? 0), 1);
    }

    /**
     * Compute total lessons count.
     */
    public function getTotalLessonsCountAttribute(): int
    {
        return $this->lessons()->count();
    }

    /**
     * Compute total duration in minutes.
     */
    public function getTotalDurationMinutesAttribute(): int
    {
        return (int) $this->lessons()->sum('duration_minutes');
    }

    /**
     * Format total duration into human readable string.
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->total_duration_minutes;
        $hours = floor($minutes / 60);
        $remMinutes = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$remMinutes}m";
        }
        return "{$remMinutes} mins";
    }

    /**
     * Formatted price string.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price <= 0) {
            return 'Free';
        }
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get course thumbnail URL or offline fallback SVG.
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        $titleEscaped = htmlspecialchars(substr($this->title, 0, 24));
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='640' height='360' viewBox='0 0 640 360'>
            <defs>
                <linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'>
                    <stop offset='0%' stop-color='#0f172a' />
                    <stop offset='100%' stop-color='#2563eb' />
                </linearGradient>
            </defs>
            <rect width='100%' height='100%' fill='url(#grad)' />
            <circle cx='320' cy='150' r='48' fill='rgba(255,255,255,0.15)' />
            <polygon points='312,132 338,150 312,168' fill='#ffffff' />
            <text x='50%' y='240' font-family='sans-serif' font-weight='600' font-size='22' fill='#ffffff' text-anchor='middle'>{$titleEscaped}</text>
        </svg>";

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
