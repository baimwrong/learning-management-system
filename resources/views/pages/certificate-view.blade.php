<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_code }}</title>

    <!-- Offline Local CSS Assets -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .cert-container {
            width: 900px;
            height: 620px;
            background: #ffffff;
            border: 12px double #1e3a8a;
            outline: 3px solid #d97706;
            padding: 50px 60px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            text-align: center;
            box-sizing: border-box;
        }

        .cert-header {
            font-size: 1.2rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .cert-title {
            font-size: 2.6rem;
            color: #d97706;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .cert-presented {
            font-size: 1rem;
            font-style: italic;
            color: #64748b;
            margin-bottom: 10px;
        }

        .cert-student {
            font-size: 2.2rem;
            color: #0f172a;
            font-weight: bold;
            border-bottom: 2px solid #cbd5e1;
            display: inline-block;
            padding: 0 40px 10px;
            margin-bottom: 20px;
        }

        .cert-course {
            font-size: 1.4rem;
            color: #1e3a8a;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .cert-seal {
            width: 90px;
            height: 90px;
        }

        .cert-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 30px;
        }

        .signature-line {
            width: 180px;
            border-bottom: 1px solid #334155;
            margin-bottom: 5px;
        }

        .print-controls {
            margin-bottom: 25px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .print-controls {
                display: none !important;
            }
            .cert-container {
                box-shadow: none;
                border-width: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls d-flex gap-3">
        <button onclick="window.print()" class="btn btn-primary px-4">
            <i class="bi bi-printer me-1"></i> Print / Save as PDF
        </button>
        <a href="{{ route('student.certificates') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Certificates
        </a>
    </div>

    <div class="cert-container">
        <div class="cert-header">Learning Management System Academic Institute</div>
        <div class="cert-title">Certificate of Completion</div>

        <div class="cert-presented">This document proudly certifies that</div>
        <div class="cert-student">{{ $certificate->user->name }}</div>

        <div class="cert-presented">has successfully completed the comprehensive technical curriculum for</div>
        <div class="cert-course">{{ $certificate->course->title }}</div>

        <div class="cert-footer">
            <div class="text-start">
                <div class="signature-line"></div>
                <div class="fw-bold small text-dark">{{ $certificate->course->instructor?->name ?? 'Course Instructor' }}</div>
                <div class="text-muted small" style="font-size: 0.75rem;">Lead Faculty Specialist</div>
            </div>

            <!-- Golden Digital Seal SVG -->
            <div>
                <svg class="cert-seal" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="46" fill="#fef3c7" stroke="#d97706" stroke-width="3"/>
                    <circle cx="50" cy="50" r="38" fill="none" stroke="#d97706" stroke-width="1.5" stroke-dasharray="3,3"/>
                    <path d="M50 15 L53 35 L73 35 L57 47 L63 67 L50 55 L37 67 L43 47 L27 35 L47 35 Z" fill="#d97706"/>
                    <text x="50" y="82" font-size="8" font-family="sans-serif" font-weight="bold" fill="#92400e" text-anchor="middle">OFFICIAL SEAL</text>
                </svg>
            </div>

            <div class="text-end">
                <div class="signature-line ms-auto"></div>
                <div class="fw-bold small text-dark">{{ $certificate->issued_at->format('F d, Y') }}</div>
                <div class="text-muted small" style="font-size: 0.75rem;">Official Date of Issuance</div>
            </div>
        </div>

        <div class="mt-4 pt-2 text-muted" style="font-size: 0.75rem;">
            Certificate Verification Code: <strong class="text-primary">{{ $certificate->certificate_code }}</strong> • Authenticate at: {{ route('certificates.verify', $certificate->certificate_code) }}
        </div>
    </div>
</body>
</html>
