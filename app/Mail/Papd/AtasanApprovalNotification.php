<?php

namespace App\Mail\Papd;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Papd\PapdRequest;

class AtasanApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $papdRequest;

    public function __construct(PapdRequest $papdRequest)
    {
        $this->papdRequest = $papdRequest;
    }

    public function build()
    {
        $approveUrl = route('papd.approve', $this->papdRequest->approval_token);
        $rejectUrl  = route('papd.reject', $this->papdRequest->approval_token);

        return $this->subject('Persetujuan Perjalanan Dinas - ' . $this->papdRequest->nama_lengkap)
                    ->markdown('admin.email.papd.atasan-approval')
                    ->with([
                        'request' => $this->papdRequest,
                        'approveUrl' => $approveUrl,
                        'rejectUrl' => $rejectUrl,
                    ]);
    }
}