<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->enum('level', ['all_levels', 'beginner', 'intermediate', 'advanced'])->default('all_levels');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('thumbnail')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->jsonb('requirements')->nullable(); // PostgreSQL JSONB array of prerequisites
            $table->jsonb('learning_outcomes')->nullable(); // PostgreSQL JSONB array of outcomes
            $table->jsonb('metadata')->nullable(); // Extended configuration
            $table->timestamps();
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order_number')->default(1);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->enum('content_type', ['video', 'reading', 'quiz', 'document'])->default('reading');
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('attachment_path')->nullable();
            $table->unsignedInteger('duration_minutes')->default(10);
            $table->unsignedInteger('order_number')->default(1);
            $table->boolean('is_preview')->default(false); // Free preview lesson
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
    }
};
