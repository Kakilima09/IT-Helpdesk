<?php

namespace App\Mail\Ga;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ga\GaRequest;
use App\Services\GaPdfService;
use Illuminate\Support\Facades\Log;

class RejectedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct(GaRequest $gaRequest)
    {
        $this->request = $gaRequest;
    }

    public function build()
    {
        $mail = $this->subject('GA Request ' . $this->request->request_no . ' Ditolak')
                    ->markdown('admin.email.ga.rejected-user')
                    ->with(['request' => $this->request]);

        try {
            $pdfService = app(GaPdfService::class);
            $pdf = $pdfService->bytes($this->request);
            if ($pdf) {
                $mail->attachData($pdf, $pdfService->fileName($this->request), ['mime' => 'application/pdf']);
            }
        } catch (\Exception $e) {
            Log::error('Gagal lampirkan PDF GA rejected: ' . $e->getMessage());
        }

        return $mail;
    }
}