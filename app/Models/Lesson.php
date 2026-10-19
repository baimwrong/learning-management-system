<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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

    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(
            Course::class,
            CourseModule::class,
            'id',        // Foreign key on course_modules table (referenced by lessons.module_id)
            'id',        // Foreign key on courses table (referenced by course_modules.course_id)
            'module_id', // Local key on lessons table
            'course_id'  // Local key on course_modules table
        );
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

    /**
     * Get safe sanitized content for rendering in player view.
     */
    public function getSanitizedContentAttribute(): string
    {
        $content = $this->content ?? '';
        // Remove script, style, applet, embed, object tags and their contents
        $content = preg_replace('#<(script|style|applet|embed|object)[^>]*>.*?</\1>#si', '', $content);
        // Remove inline event handlers (onload, onerror, onclick, etc.)
        $content = preg_replace('#\s*on\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)#si', '', $content);
        // Disallow javascript: links
        $content = preg_replace('#href\s*=\s*(["\'])\s*javascript:[^"\']*\1#si', 'href="#"', $content);
        return $content;
    }

    /**
     * Get safe video URL for iframe embedding.
     */
    public function getSafeVideoUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }
        $url = trim($this->video_url);
        if (preg_match('#^https?://#i', $url) || str_starts_with($url, '/')) {
            return $url;
        }
        return null;
    }
}
