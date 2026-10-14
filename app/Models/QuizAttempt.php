<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score_percentage',
        'earned_points',
        'total_points',
        'is_passed',
        'answers_submitted',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score_percentage' => 'decimal:2',
            'is_passed' => 'boolean',
            'answers_submitted' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
