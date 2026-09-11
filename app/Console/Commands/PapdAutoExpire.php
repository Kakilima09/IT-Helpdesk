<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Papd\PapdRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\Papd\RejectedUserNotification;
use Illuminate\Support\Facades\Log;
use App\Notifications\PapdNotification;
use Illuminate\Support\Facades\Notification;

class PapdAutoExpire extends Command
{
    protected $signature = 'papd:auto-expire';
    protected $description = 'Auto reject PAPD requests pending > 12 hours';

    public function handle()
    {
        $cutoff = now()->subHours(12);

        $requests = PapdRequest::where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($requests->isEmpty()) {
            $this->info('Tidak ada permintaan yang melewati batas.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($requests as $papd) {
            // Update status
            $papd->update([
                'status' => 'expired',       // Opsi A
                // 'status' => 'rejected',   // Opsi B
                'rejected_at' => now(),
                'approval_token' => null,
                // 'auto_expired_at' => now(), // Opsi B
            ]);

            //Notifikasi bell
            try {
                $user = User::find($papd->user_id);
                if ($user) {
                    $user->notify(new PapdNotification(
                        $papd->id,
                        'Permintaan PAPD Kadaluwarsa',
                        'Permintaan PAPD Anda telah kadaluwarsa karena tidak ada respons dari atasan dalam 12 jam.',
                        'expired',
                        route('papd.show', $papd->id)
                    ));
                    \Log::info('Notifikasi database expired terkirim ke pemohon: ' . $user->email);
                }
            } catch (\Exception $e) {
                \Log::error('Gagal kirim notifikasi database expired: ' . $e->getMessage());
            }

            // Kirim email ke pemohon
            try {
                // Gunakan Mailable yang sudah ada atau buat khusus
                Mail::to($papd->email)->send(new RejectedUserNotification($papd));
                Log::info("Auto-expired PAPD #{$papd->id} - email terkirim ke {$papd->email}");
            } catch (\Exception $e) {
                Log::error("Auto-expire email gagal untuk #{$papd->id}: " . $e->getMessage());
            }

            $count++;
        }

        $this->info("Auto-expired $count permintaan.");
        return Command::SUCCESS;
    }
}