<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BulkImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BulkImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_import_service_processes_users_csv_and_saves_error_telemetry(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $csvContent = "name,email,role,password,phone,bio\n" .
            "John Doe,john@example.com,student,Secret123!,+123456789,Student bio\n" .
            "Jane Smith,invalid-email,student,Secret123!,+987654321,Invalid row\n";

        $file = UploadedFile::fake()->createWithContent('users.csv', $csvContent);

        $service = new BulkImportService();
        $bulkImport = $service->processImport($file, 'users', $admin);

        $this->assertEquals('completed', $bulkImport->status);
        $this->assertEquals(2, $bulkImport->total_rows);
        $this->assertEquals(1, $bulkImport->successful_rows);
        $this->assertEquals(1, $bulkImport->failed_rows);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'invalid-email',
        ]);

        $this->assertNotEmpty($bulkImport->error_log);
    }
}
