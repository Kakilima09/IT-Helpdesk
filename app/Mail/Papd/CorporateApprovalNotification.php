<?php

namespace App\Mail\Papd;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Papd\PapdRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CorporateApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $papdRequest;

    public function __construct(PapdRequest $papdRequest)
    {
        $this->papdRequest = $papdRequest;
    }

    public function build()
    {
        $approveUrl = URL::temporarySignedRoute(
            'admin.papd.corporateApproveFromEmail',
            now()->addDays(7),
            ['id' => $this->papdRequest->id]
        );

        $pdfContent = null;
        try {
            $pdf = Pdf::loadView('user.pdf.papd-approval', [
                'papdRequest' => $this->papdRequest,
                'logo' => null,
            ]);
            $pdfContent = $pdf->output();
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF untuk approval corporate: ' . $e->getMessage());
        }

        $mail = $this->subject('Persetujuan Corporate PAPD - ' . $this->papdRequest->nama_lengkap)
                    ->markdown('admin.email.papd.corporate-approval')
                    ->with([
                        'request' => $this->papdRequest,
                        'approveUrl' => $approveUrl,
                    ]);

        if ($pdfContent) {
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->papdRequest->nama_lengkap);
            $fileName = 'PAPD-' . $cleanName . '.pdf';

            $mail->attachData($pdfContent, $fileName, [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
