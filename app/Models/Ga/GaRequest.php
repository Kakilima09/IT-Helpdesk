<?php

namespace App\Models\Ga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Customer;

class GaRequest extends Model
{
    use HasFactory;

    protected $table = 'ga_requests';

    const STATUS_PENDING_L1 = 'pending_l1';
    const STATUS_PENDING_L2 = 'pending_l2';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    const TYPE_GOODS = 'goods';
    const TYPE_SERVICE = 'service';

    const PREF_EMAIL = 'email';
    const PREF_WHATSAPP = 'whatsapp';
    const PREF_BOTH = 'both';

    protected $fillable = [
        'request_no', 'requester_type', 'user_id', 'customer_id',
        'nama_lengkap', 'jabatan', 'departemen', 'entitas', 'email', 'no_hp',
        'atasan_user_id', 'atasan_nama', 'atasan_email',
        'l1_approver_user_id', 'l1_approver_name', 'l1_approver_email',
        'l2_approver_user_id', 'l2_approver_name', 'l2_approver_email',
        'status', 'needs_layer2', 'max_goods_price', 'goods_total', 'services_total', 'total_amount',
        'l1_token', 'l2_token',
        'l1_approved_at', 'l2_approved_at', 'approved_at', 'rejected_at', 'expired_at', 'cancelled_at',
        'rejected_by_user_id', 'reject_reason', 'notes',
    ];

    protected $casts = [
        'needs_layer2' => 'boolean',
        'max_goods_price' => 'float',
        'goods_total' => 'float',
        'services_total' => 'float',
        'total_amount' => 'float',
        'l1_approved_at' => 'datetime',
        'l2_approved_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expired_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->l1_token)) {
                $model->l1_token = (string) Str::uuid();
            }
            if (empty($model->l2_token)) {
                $model->l2_token = (string) Str::uuid();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(GaRequestItem::class, 'ga_request_id');
    }

    public function goods()
    {
        return $this->items()->where('item_type', self::TYPE_GOODS);
    }

    public function services()
    {
        return $this->items()->where('item_type', self::TYPE_SERVICE);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function atasanUser()
    {
        return $this->belongsTo(User::class, 'atasan_user_id');
    }

    public function l1Approver()
    {
        return $this->belongsTo(User::class, 'l1_approver_user_id');
    }

    public function l2Approver()
    {
        return $this->belongsTo(User::class, 'l2_approver_user_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    public function scopePendingL1($query)
    {
        return $query->where('status', self::STATUS_PENDING_L1);
    }

    public function scopePendingL2($query)
    {
        return $query->where('status', self::STATUS_PENDING_L2);
    }

    public function isPending()
    {
        return in_array($this->status, [self::STATUS_PENDING_L1, self::STATUS_PENDING_L2]);
    }

    public function statusLabel()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }
}