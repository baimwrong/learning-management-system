<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_courses_requirements_gin ON courses USING gin (requirements)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_courses_learning_outcomes_gin ON courses USING gin (learning_outcomes)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_quiz_questions_options_gin ON quiz_questions USING gin (options)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_bulk_imports_error_log_gin ON bulk_imports USING gin (error_log)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_courses_requirements_gin');
            DB::statement('DROP INDEX IF EXISTS idx_courses_learning_outcomes_gin');
            DB::statement('DROP INDEX IF EXISTS idx_quiz_questions_options_gin');
            DB::statement('DROP INDEX IF EXISTS idx_bulk_imports_error_log_gin');
        }
    }
};
