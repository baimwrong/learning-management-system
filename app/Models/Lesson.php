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
        if (trim($content) === '') {
            return '';
        }

        // 1. Remove dangerous blocks and their contents entirely
        $dangerousBlocks = 'script|style|applet|embed|object|iframe|frame|frameset|meta|link|base|form|input|button|textarea|select|option|svg|math|template';
        $content = preg_replace("#<($dangerousBlocks)[^>]*>.*?</\\1>#si", '', $content);
        // Strip unclosed dangerous tags
        $content = preg_replace("#</?($dangerousBlocks)[^>]*>#si", '', $content);

        // 2. Strip all inline event handlers (on*) regardless of whitespace, slashes, or quoting
        $content = preg_replace('#[/\\s]+on[a-z0-9_-]+\\s*=\\s*("[^"]*"|\'[^\']*\'|[^\\s>]+)#si', '', $content);
        $content = preg_replace('#[/\\s]+on[a-z0-9_-]+(?=[\\s/>])#si', '', $content);

        // 3. Disallow dangerous pseudo-protocols (javascript:, vbscript:, data:) in any attribute
        $content = preg_replace('#(href|src|action|data)\\s*=\\s*(["\']?)\\s*(javascript|vbscript|data):[^"\'\\s>]*\\2#si', '$1="#"', $content);

        // 4. Strip dangerous CSS expressions or url imports in style attributes
        $content = preg_replace('#style\\s*=\\s*(["\']).*?(expression|behavior|javascript|url\\s*\\().*?\\1#si', '', $content);

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
