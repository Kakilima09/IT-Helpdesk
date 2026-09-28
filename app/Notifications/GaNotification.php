<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GaNotification extends Notification
{
    use Queueable;

    protected $gaId;
    protected $subject;
    protected $message;
    protected $status;
    protected $link;

    public function __construct($gaId, $subject, $message, $status, $link = null)
    {
        $this->gaId = $gaId;
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
            'ga_id' => $this->gaId,
            'mailsubject' => $this->subject,
            'mailtext' => $this->message,
            'status' => $this->status,
            'ga_status' => $this->status,
            'link' => $this->link ?? route('ga.show', $this->gaId),
        ];
    }
}