<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressAndCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_recalculate_progress_on_empty_course_does_not_complete_or_award_certificate(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Empty Course',
            'slug' => 'empty-course',
            'instructor_id' => $instructor->id,
            'is_published' => true,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
        ]);

        $progress = $enrollment->recalculateProgress();

        $this->assertEquals(0.00, $progress);
        $this->assertEquals('active', $enrollment->fresh()->status);
        $this->assertDatabaseMissing('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_recalculate_progress_awards_certificate_at_100_percent_and_resets_if_uncompleted(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Full Course',
            'slug' => 'full-course',
            'instructor_id' => $instructor->id,
            'is_published' => true,
        ]);

        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
        ]);

        $lesson1 = Lesson::create(['module_id' => $module->id, 'title' => 'Lesson 1', 'slug' => 'l1']);
        $lesson2 = Lesson::create(['module_id' => $module->id, 'title' => 'Lesson 2', 'slug' => 'l2']);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percentage' => 0.00,
        ]);

        // Complete lesson 1 -> 50%
        LessonCompletion::create([
            'user_id' => $student->id,
            'lesson_id' => $lesson1->id,
            'course_id' => $course->id,
        ]);

        $progress1 = $enrollment->recalculateProgress();
        $this->assertEquals(50.00, $progress1);
        $this->assertEquals('active', $enrollment->fresh()->status);

        // Complete lesson 2 -> 100%
        $comp2 = LessonCompletion::create([
            'user_id' => $student->id,
            'lesson_id' => $lesson2->id,
            'course_id' => $course->id,
        ]);

        $progress2 = $enrollment->recalculateProgress();
        $this->assertEquals(100.00, $progress2);
        $this->assertEquals('completed', $enrollment->fresh()->status);
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        // Uncomplete lesson 2 -> 50%, status must revert to active
        $comp2->delete();
        $progress3 = $enrollment->recalculateProgress();
        $this->assertEquals(50.00, $progress3);
        $this->assertEquals('active', $enrollment->fresh()->status);
        $this->assertNull($enrollment->fresh()->completed_at);
    }
}
