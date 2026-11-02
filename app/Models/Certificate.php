<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_code',
        'issued_at',
        'file_path',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'metadata' => 'array',
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

    public function getRouteKeyName(): string
    {
        return 'certificate_code';
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify', $this->certificate_code);
    }

    /**
     * Check if certificate is currently valid (student has 100% completed enrollment).
     */
    public function isValid(): bool
    {
        $enrollment = Enrollment::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->first();

        return $enrollment && $enrollment->status === 'completed' && (float)$enrollment->progress_percentage >= 100.00;
    }
}
