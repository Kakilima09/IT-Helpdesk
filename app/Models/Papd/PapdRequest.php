<?php

namespace App\Models\Papd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PapdRequest extends Model
{
    use HasFactory;

    protected $table = 'papd_requests';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';
    const CLOSING_PENDING = 'pending';
    const CLOSING_DONE    = 'done';
    const CLOSING_CANCEL  = 'cancel';

    protected $fillable = [
        'user_id', 'nama_lengkap', 'id_karyawan', 'nik_ktp', 'jabatan', 'departemen',
        'entitas', 'atasan_nama', 'atasan_email', 'no_hp', 'email',
        'ttl', 'no_paspor', 'exp_date_paspor',
        'jenis_perjalanan', 'nama_paspor', 'kota_tujuan', 'agenda', 'no_sppd',
        'tanggal_keberangkatan', 'jam_keberangkatan', 'tanggal_kepulangan', 'jam_kepulangan', 'durasi_hari', 'pembebanan_biaya',
        'moda_transportasi', 'kelas', 'rute', 'detail_maskapai',
        'no_penerbangan', 'bagasi_tambahan', 'transportasi_lokal',
        'opsi_transportasi',
        'hotel_reservasi', 'nama_hotel', 'lokasi_hotel', 'alamat_hotel',
        'check_in', 'check_out', 'jumlah_kamar', 'permintaan_khusus',
        'notes', 'status', 'approval_token','closing_status',
        'closed_at',
        'closing_note',
    ];

    protected $casts = [
        'opsi_transportasi' => 'array',
        'ttl' => 'date',
        'exp_date_paspor' => 'date',
        'tanggal_keberangkatan' => 'date',
        'check_in' => 'date',
        'check_out' => 'date',
        'bagasi_tambahan' => 'boolean',
        'transportasi_lokal' => 'boolean',
        'hotel_reservasi' => 'boolean',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Generate token otomatis saat membuat
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->approval_token)) {
                $model->approval_token = (string) Str::uuid();
            }
        });
    }

    // Scope untuk pending
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeClosingPending($query)
    {
        return $query->where('closing_status', self::CLOSING_PENDING);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('closing_status', [self::CLOSING_DONE, self::CLOSING_CANCEL]);
    }
}