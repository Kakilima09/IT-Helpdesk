<?php

namespace App\Mail\Papd;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Papd\PapdRequest;

class RejectedUserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $papdRequest;

    public function __construct(PapdRequest $papdRequest)
    {
        $this->papdRequest = $papdRequest;
    }

    public function build()
    {
        return $this->subject('Permintaan Perjalanan Dinas Ditolak')
                    ->markdown('admin.email.papd.rejected-user')
                    ->with('request', $this->papdRequest);
    }
}