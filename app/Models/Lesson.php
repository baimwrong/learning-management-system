<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'content_type',
        'content',
        'video_url',
        'attachment_path',
        'duration_minutes',
        'order_number',
        'is_preview',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function course(): BelongsTo
    {
        return $this->module ? $this->module->course() : null;
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class)->whereNull('parent_id')->orderBy('is_pinned', 'desc')->latest();
    }

    public function isCompletedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->completions()->where('user_id', $user->id)->exists();
    }
}
