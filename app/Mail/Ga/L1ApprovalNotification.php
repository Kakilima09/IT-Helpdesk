<?php

namespace App\Mail\Ga;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ga\GaRequest;
use App\Services\GaPdfService;
use Illuminate\Support\Facades\Log;

class L1ApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $approveUrl;
    public $rejectUrl;

    public function __construct(GaRequest $gaRequest)
    {
        $this->request = $gaRequest;
        $this->approveUrl = route('ga.approve.l1', $gaRequest->l1_token);
        $this->rejectUrl = route('ga.reject.l1', $gaRequest->l1_token);
    }

    public function build()
    {
        $mail = $this->subject('Persetujuan GA Request ' . $this->request->request_no)
                    ->markdown('admin.email.ga.l1-approval')
                    ->with([
                        'request' => $this->request,
                        'approveUrl' => $this->approveUrl,
                        'rejectUrl' => $this->rejectUrl,
                    ]);

        try {
            $pdfService = app(GaPdfService::class);
            $pdf = $pdfService->bytes($this->request);
            if ($pdf) {
                $mail->attachData($pdf, $pdfService->fileName($this->request), ['mime' => 'application/pdf']);
            }
        } catch (\Exception $e) {
            Log::error('Gagal lampirkan PDF GA L1: ' . $e->getMessage());
        }

        return $mail;
    }
}