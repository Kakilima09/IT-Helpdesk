<?php

namespace App\Mail\Papd;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Papd\PapdRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ApprovedCorporateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $papdRequest;

    public function __construct(PapdRequest $papdRequest)
    {
        $this->papdRequest = $papdRequest;
    }

    public function build()
    {
        // Generate PDF
        $pdfContent = null;
        try {
            $pdf = Pdf::loadView('user.pdf.papd-approval', ['papd' => $this->papdRequest]);
            $pdfContent = $pdf->output();
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF untuk corporate: ' . $e->getMessage());
        }

        $mail = $this->subject('Data PAPD untuk Pemesanan Tiket/Hotel')
                    ->markdown('admin.email.papd.approved-corporate')
                    ->with('request', $this->papdRequest);

        if ($pdfContent) {
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->papdRequest->nama_lengkap);
            $fileName = 'PAPD-' . $cleanName . '.pdf';

            $mail->attachData($pdfContent, $fileName, [
                'mime' => 'application/pdf',
            ]);
        } else {
            Log::warning('PDF tidak dilampirkan untuk corporate.');
        }

        return $mail;
    }
}