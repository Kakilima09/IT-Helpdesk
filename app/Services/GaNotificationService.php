<?php

namespace App\Services;

use App\Mail\Ga\L1ApprovalNotification;
use App\Mail\Ga\L2ApprovalNotification;
use App\Mail\Ga\ApprovedNotification;
use App\Mail\Ga\RejectedNotification;
use App\Mail\Ga\ExpiredNotification;
use App\Models\User;
use App\Models\Customer;
use App\Models\Ga\GaRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GaNotificationService
{
    protected $whatsapp;
    protected $pdfService;

    public function __construct()
    {
        $this->whatsapp = app(WhatsAppService::class);
        $this->pdfService = app(GaPdfService::class);
    }

    /**
     * Kirim email dan/atau WhatsApp sesuai preferensi penerima.
     */
    public function dispatch($recipient, $mailable, $bodyText, GaRequest $gaRequest, $attachPdf = true)
    {
        $pref = $recipient->notification_pref ?? 'email';
        $email = $recipient->email ?? null;
        $phone = $recipient->phone ?? null;

        if (in_array($pref, ['email', 'both']) && $email) {
            try {
                Mail::to($email)->send($mailable);
            } catch (\Exception $e) {
                Log::error('Email GA gagal ke ' . $email . ': ' . $e->getMessage());
            }
        }

        if (in_array($pref, ['whatsapp', 'both']) && $phone && $this->whatsapp->isConfigured()) {
            $target = $this->whatsapp->normalizeNumber($phone);
            $sent = false;

            if ($attachPdf) {
                $pdfBytes = $this->pdfService->bytes($gaRequest);
                if ($pdfBytes) {
                    $sent = $this->whatsapp->sendDocument(
                        $target,
                        base64_encode($pdfBytes),
                        $this->pdfService->fileName($gaRequest),
                        $bodyText
                    );
                }
            }

            if (!$sent) {
                $this->whatsapp->sendText($target, $bodyText);
            }
        }
    }

    public function notifyL1(GaRequest $gaRequest)
    {
        $recipient = $this->approverRecipient($gaRequest->l1_approver_user_id, $gaRequest->l1_approver_email);
        $this->dispatch($recipient, new L1ApprovalNotification($gaRequest), $this->approvalText($gaRequest, 'l1'), $gaRequest);
    }

    public function notifyL2(GaRequest $gaRequest)
    {
        $recipient = $this->approverRecipient($gaRequest->l2_approver_user_id, $gaRequest->l2_approver_email);
        $this->dispatch($recipient, new L2ApprovalNotification($gaRequest), $this->approvalText($gaRequest, 'l2'), $gaRequest);
    }

    public function notifyRequesterApproved(GaRequest $gaRequest)
    {
        $recipient = $this->requesterRecipient($gaRequest);
        $text = "GA Request {$gaRequest->request_no} telah DISETUJUI.\n"
            . "Total: Rp " . number_format($gaRequest->total_amount, 0, ',', '.') . "\n"
            . "Status: " . $this->statusLabel($gaRequest) . "\n"
            . "Detail & dokumen PDF terlampir.";
        $this->dispatch($recipient, new ApprovedNotification($gaRequest), $text, $gaRequest);
    }

    public function notifyRequesterRejected(GaRequest $gaRequest)
    {
        $recipient = $this->requesterRecipient($gaRequest);
        $reason = $gaRequest->reject_reason ?? '-';
        $text = "GA Request {$gaRequest->request_no} telah DITOLAK.\n"
            . "Alasan: {$reason}\n"
            . "Detail & dokumen PDF terlampir.";
        $this->dispatch($recipient, new RejectedNotification($gaRequest), $text, $gaRequest);
    }

    public function notifyRequesterExpired(GaRequest $gaRequest)
    {
        $recipient = $this->requesterRecipient($gaRequest);
        $text = "GA Request {$gaRequest->request_no} telah KADALUWARSA karena tidak ada persetujuan dalam batas waktu.\n"
            . "Silakan ajukan ulang jika masih dibutuhkan.";
        $this->dispatch($recipient, new ExpiredNotification($gaRequest), $text, $gaRequest);
    }

    protected function approvalText(GaRequest $r, $layer)
    {
        $approveUrl = $layer === 'l2' ? route('ga.approve.l2', $r->l2_token) : route('ga.approve.l1', $r->l1_token);
        $rejectUrl = $layer === 'l2' ? route('ga.reject.l2', $r->l2_token) : route('ga.reject.l1', $r->l1_token);

        $text = "Persetujuan GA Request {$r->request_no}\n"
            . "Pengaju: {$r->nama_lengkap}\n"
            . "Total: Rp " . number_format($r->total_amount, 0, ',', '.') . "\n";

        if ($r->needs_layer2) {
            $text .= "Catatan: diverifikasi level 2 karena ada item barang > threshold.\n";
        }

        $text .= "Setujui: {$approveUrl}\nTolak: {$rejectUrl}";
        return $text;
    }

    protected function statusLabel(GaRequest $r)
    {
        return ucwords(str_replace('_', ' ', $r->status));
    }

    protected function approverRecipient($userId, $fallbackEmail)
    {
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                return $this->recipientFor($user->email, $user->phone, $user->notification_pref ?? 'email');
            }
        }
        return $this->recipientFor($fallbackEmail);
    }

    protected function requesterRecipient(GaRequest $r)
    {
        if ($r->requester_type === 'user' && $r->user_id) {
            $user = User::find($r->user_id);
            if ($user) {
                return $this->recipientFor($user->email, $user->phone, $user->notification_pref ?? 'email');
            }
        }

        if ($r->customer_id) {
            $customer = Customer::find($r->customer_id);
            if ($customer) {
                return $this->recipientFor($customer->email, $customer->phone, $customer->notification_pref ?? 'email');
            }
        }

        return $this->recipientFor($r->email, $r->no_hp, 'email');
    }

    protected function recipientFor($email, $phone = null, $pref = 'email')
    {
        $r = new \stdClass();
        $r->email = $email;
        $r->phone = $phone;
        $r->notification_pref = $pref;
        return $r;
    }
}