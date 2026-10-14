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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('time_limit_minutes')->default(0); // 0 = unlimited
            $table->unsignedInteger('passing_score_percentage')->default(70);
            $table->unsignedInteger('max_attempts')->default(3); // 0 = unlimited
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'true_false', 'short_answer'])->default('single_choice');
            $table->jsonb('options')->nullable(); // Array of options: [{"key": "A", "text": "Option text"}, ...]
            $table->jsonb('correct_answers'); // Array or string: ["A"] or ["A", "C"] or "True"
            $table->unsignedInteger('points')->default(1);
            $table->text('explanation')->nullable();
            $table->unsignedInteger('order_number')->default(1);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('score_percentage', 5, 2)->default(0.00);
            $table->unsignedInteger('earned_points')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->boolean('is_passed')->default(false);
            $table->jsonb('answers_submitted')->nullable(); // Detailed submission map
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
