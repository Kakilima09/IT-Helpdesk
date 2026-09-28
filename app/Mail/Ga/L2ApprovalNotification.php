<?php

namespace App\Mail\Ga;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ga\GaRequest;
use App\Services\GaPdfService;
use Illuminate\Support\Facades\Log;

class L2ApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $approveUrl;
    public $rejectUrl;

    public function __construct(GaRequest $gaRequest)
    {
        $this->request = $gaRequest;
        $this->approveUrl = route('ga.approve.l2', $gaRequest->l2_token);
        $this->rejectUrl = route('ga.reject.l2', $gaRequest->l2_token);
    }

    public function build()
    {
        $mail = $this->subject('Persetujuan GA Request ' . $this->request->request_no . ' (Level 2)')
                    ->markdown('admin.email.ga.l2-approval')
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
            Log::error('Gagal lampirkan PDF GA L2: ' . $e->getMessage());
        }

        return $mail;
    }
}