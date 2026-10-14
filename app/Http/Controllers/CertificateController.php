<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Public Certificate Verification.
     */
    public function verify(?string $code = null, Request $request)
    {
        $searchCode = $code ?? $request->input('code');
        $certificate = null;

        if ($searchCode) {
            $certificate = Certificate::where('certificate_code', strtoupper(trim($searchCode)))
                ->with(['user', 'course.instructor'])
                ->first();
        }

        return view('pages.certificate-verify', compact('certificate', 'searchCode'));
    }

    /**
     * View and Print Certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'course.instructor']);
        return view('pages.certificate-view', compact('certificate'));
    }
}
