<?php

namespace App\Services;

use App\Models\Ga\GaRequest;
use App\Models\User;
use App\Models\Customer;
use App\Notifications\GaNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class GaWorkflowService
{
    public function approve(GaRequest $gaRequest, $layer, $actorUserId = null)
    {
        $now = now();

        if ($layer == 'l2') {
            $gaRequest->update([
                'status' => GaRequest::STATUS_APPROVED,
                'l2_approved_at' => $now,
                'approved_at' => $now,
                'l2_token' => null,
            ]);

            $this->notifyRequester($gaRequest, 'GA Request Disetujui', 'GA Request Anda telah disetujui pada level 2.', GaRequest::STATUS_APPROVED);
            app(GaNotificationService::class)->notifyRequesterApproved($gaRequest);
            return;
        }

        $gaRequest->update([
            'l1_approved_at' => $now,
            'l1_token' => null,
        ]);

        if ($gaRequest->needs_layer2) {
            list($l2Id, $l2Name, $l2Email) = $this->resolveL2Approver();

            $gaRequest->update([
                'status' => GaRequest::STATUS_PENDING_L2,
                'l2_approver_user_id' => $l2Id,
                'l2_approver_name' => $l2Name,
                'l2_approver_email' => $l2Email,
            ]);

            $this->notifyRequester($gaRequest, 'GA Request Menunggu Level 2', 'Permintaan Anda disetujui atasan dan diteruskan ke level 2.', GaRequest::STATUS_PENDING_L2);
            app(GaNotificationService::class)->notifyL2($gaRequest);
            return;
        }

        $gaRequest->update([
            'status' => GaRequest::STATUS_APPROVED,
            'approved_at' => $now,
        ]);

        $this->notifyRequester($gaRequest, 'GA Request Disetujui', 'GA Request Anda telah disetujui.', GaRequest::STATUS_APPROVED);
        app(GaNotificationService::class)->notifyRequesterApproved($gaRequest);
    }

    public function reject(GaRequest $gaRequest, $actorUserId = null, $reason = null)
    {
        $gaRequest->update([
            'status' => GaRequest::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejected_by_user_id' => $actorUserId,
            'reject_reason' => $reason ?: 'Ditolak oleh approver.',
            'l1_token' => null,
            'l2_token' => null,
        ]);

        $this->notifyRequester($gaRequest, 'GA Request Ditolak', 'GA Request Anda telah ditolak.', GaRequest::STATUS_REJECTED);
        app(GaNotificationService::class)->notifyRequesterRejected($gaRequest);
    }

    public function resolveL1Approver($atasanUserId = null)
    {
        if ($atasanUserId) {
            $atasan = User::find($atasanUserId);
            if ($atasan) {
                return [$atasan->id, $this->fullName($atasan), $atasan->email];
            }
        }

        $staff = $this->roleUser(config('ga.l1_role', 'ga_staff'));
        if (!$staff) {
            $staff = $this->roleUser('corporate');
        }
        if (!$staff) {
            $staff = $this->roleUser('superadmin');
        }

        if (!$staff) {
            return [null, null, null];
        }

        return [$staff->id, $this->fullName($staff), $staff->email];
    }

    public function resolveL2Approver()
    {
        $manager = $this->roleUser(config('ga.l2_role', 'ga_manager'));
        if (!$manager) {
            $manager = $this->roleUser('corporate');
        }
        if (!$manager) {
            $manager = $this->roleUser('superadmin');
        }

        if (!$manager) {
            return [null, null, null];
        }

        return [$manager->id, $this->fullName($manager), $manager->email];
    }

    public function roleUser($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user = $role->users()->where('users.status', '1')->orderBy('users.id')->first();
            if ($user) {
                return $user;
            }
        }

        return User::where('role', $roleName)->where('status', '1')->orderBy('id')->first();
    }

    public function notifyRequester(GaRequest $gaRequest, $subject, $message, $status)
    {
        if ($gaRequest->requester_type == 'user' && $gaRequest->user_id) {
            $user = User::find($gaRequest->user_id);
            if ($user) {
                Notification::send($user, new GaNotification($gaRequest->id, $subject, $message, $status, route('ga.show', $gaRequest->id)));
            }
        } elseif ($gaRequest->customer_id) {
            $customer = Customer::find($gaRequest->customer_id);
            if ($customer) {
                Notification::send($customer, new GaNotification($gaRequest->id, $subject, $message, $status, route('ga.show', $gaRequest->id)));
            }
        }
    }

    public function fullName($user)
    {
        if (!empty($user->name)) {
            return $user->name;
        }
        if (!empty($user->firstname)) {
            return $user->firstname . ' ' . ($user->lastname ?? '');
        }
        return $user->email;
    }
}