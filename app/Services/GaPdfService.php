<?php

namespace App\Services;

use App\Models\Ga\GaRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class GaPdfService
{
    public function generate(GaRequest $gaRequest)
    {
        $logoPath = public_path('assets/images/logo.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('user.pdf.ga-request-form', [
            'request' => $gaRequest,
            'logo' => $logoBase64,
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }

    public function bytes(GaRequest $gaRequest)
    {
        try {
            return $this->generate($gaRequest)->output();
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF GA: ' . $e->getMessage());
            return null;
        }
    }

    public function fileName(GaRequest $gaRequest)
    {
        $clean = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $gaRequest->nama_lengkap ?? '');
        return 'GA-' . ($gaRequest->request_no ?? $gaRequest->id) . '-' . $clean . '.pdf';
    }
}