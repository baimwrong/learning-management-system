<?php

namespace App\Http\Controllers;

use App\Models\BulkImport;
use App\Services\BulkImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BulkImportController extends Controller
{
    protected BulkImportService $importService;

    public function __construct(BulkImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display the Admin Bulk Import Management Center.
     */
    public function index()
    {
        $imports = BulkImport::with('user')->latest()->paginate(15);
        $stats = [
            'total_imports' => BulkImport::count(),
            'successful_imports' => BulkImport::where('status', 'completed')->count(),
            'failed_imports' => BulkImport::where('status', 'failed')->count(),
            'total_rows_imported' => BulkImport::sum('successful_rows'),
        ];

        return view('admin.bulk-import.index', compact('imports', 'stats'));
    }

    /**
     * Handle bulk CSV file upload and execute processing.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'import_type' => 'required|in:users,courses,questions,enrollments',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        $file = $request->file('csv_file');
        $type = $request->input('import_type');

        try {
            if (config('queue.default') !== 'sync') {
                $bulkImport = $this->importService->createImportRecord($file, $type, auth()->user());
                \App\Jobs\ProcessBulkImportJob::dispatch($bulkImport);

                return redirect()->route('admin.bulk-import.show', $bulkImport)
                    ->with('info', "Bulk import of {$type} queued for background processing! You can track its real-time progress here.");
            }

            $bulkImport = $this->importService->processImport($file, $type, auth()->user());

            if ($bulkImport->status === 'completed') {
                return redirect()->route('admin.bulk-import.show', $bulkImport)
                    ->with('success', "Bulk import of {$type} completed successfully! Processed {$bulkImport->successful_rows} rows.");
            } else {
                return redirect()->route('admin.bulk-import.show', $bulkImport)
                    ->with('warning', "Bulk import finished with errors. {$bulkImport->failed_rows} out of {$bulkImport->total_rows} rows failed.");
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed to initiate: ' . $e->getMessage());
        }
    }

    /**
     * Display details and error log of a specific bulk import.
     */
    public function show(BulkImport $bulkImport)
    {
        return view('admin.bulk-import.show', compact('bulkImport'));
    }

    /**
     * Download pre-configured CSV template with sample data.
     */
    public function downloadTemplate(string $type)
    {
        if (!in_array($type, ['users', 'courses', 'questions', 'enrollments'])) {
            abort(404, 'Invalid template type.');
        }

        $csvContent = $this->importService->getCsvTemplate($type);
        $filename = "template_{$type}.csv";

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Delete an import history record and remove associated stored file.
     */
    public function destroy(BulkImport $bulkImport)
    {
        if ($bulkImport->filename && \Illuminate\Support\Facades\Storage::disk('local')->exists($bulkImport->filename)) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($bulkImport->filename);
        }

        $bulkImport->delete();
        return back()->with('success', 'Import log record and stored file deleted.');
    }
}
