<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ga\GaRequest;
use App\Services\GaWorkflowService;
use App\Services\GaNotificationService;
use Illuminate\Support\Facades\Log;

class GaAutoExpire extends Command
{
    protected $signature = 'ga:auto-expire';
    protected $description = 'Auto expire GA requests pending melebihi batas jam';

    public function handle()
    {
        $hours = (int) config('ga.expire_hours', 24);
        $cutoff = now()->subHours($hours);

        $requests = GaRequest::whereIn('status', [GaRequest::STATUS_PENDING_L1, GaRequest::STATUS_PENDING_L2])
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($requests->isEmpty()) {
            $this->info('Tidak ada GA request yang melewati batas.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($requests as $gaRequest) {
            $gaRequest->update([
                'status' => GaRequest::STATUS_EXPIRED,
                'expired_at' => now(),
                'l1_token' => null,
                'l2_token' => null,
            ]);

            try {
                app(GaWorkflowService::class)->notifyRequester(
                    $gaRequest,
                    'GA Request Kadaluwarsa',
                    'GA Request Anda telah kadaluwarsa karena tidak ada persetujuan dalam ' . $hours . ' jam.',
                    GaRequest::STATUS_EXPIRED
                );
            } catch (\Exception $e) {
                Log::error('Gagal notifikasi expired GA #' . $gaRequest->id . ': ' . $e->getMessage());
            }

            try {
                app(GaNotificationService::class)->notifyRequesterExpired($gaRequest);
            } catch (\Exception $e) {
                Log::error('Gagal kirim email/WA expired GA #' . $gaRequest->id . ': ' . $e->getMessage());
            }

            $count++;
        }

        $this->info("Auto-expired $count GA request.");
        return Command::SUCCESS;
    }
}