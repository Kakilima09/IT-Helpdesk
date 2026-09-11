<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PapdNotification extends Notification
{
    use Queueable;

    protected $papdId;
    protected $subject;
    protected $message;
    protected $status;
    protected $link;

    public function __construct($papdId, $subject, $message, $status, $link = null)
    {
        $this->papdId = $papdId;
        $this->subject = $subject;
        $this->message = $message;
        $this->status = $status;
        $this->link = $link;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'papd_id' => $this->papdId,
            'mailsubject' => $this->subject,
            'mailtext' => $this->message,
            'status' => $this->status,
            'link' => $this->link ?? route('papd.show', $this->papdId),
            'papd_status' => $this->status,
        ];
    }
}