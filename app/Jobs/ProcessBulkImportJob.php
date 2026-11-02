<?php

namespace App\Jobs;

use App\Models\BulkImport;
use App\Services\BulkImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessBulkImportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public BulkImport $bulkImport)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(BulkImportService $service): void
    {
        $service->executeProcessing($this->bulkImport);
    }
}
