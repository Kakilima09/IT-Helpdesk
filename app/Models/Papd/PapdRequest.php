<?php

namespace App\Models\Papd;

use App\Models\User;
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
    const CORP_PENDING    = 'pending';
    const CORP_APPROVED   = 'approved';
    const CORP_REJECTED   = 'rejected';

    protected $fillable = [
        'user_id', 'jenis_pemohon', 'nama_lengkap', 'id_karyawan', 'nik_ktp', 'jabatan', 'departemen',
        'atasan_nama', 'atasan_email', 'no_hp', 'email',
        'ttl', 'no_paspor', 'exp_date_paspor',
        'jenis_perjalanan', 'nama_sesuai_ktp', 'nama_paspor', 'kota_tujuan', 'kota_asal', 'kode_asal', 'kode_tujuan', 'agenda',
        'role_perjalanan', 'nama_anggota_team', 'jumlah_anggota_team', 'no_sppd',
        'tanggal_keberangkatan', 'jam_keberangkatan', 'tanggal_kepulangan', 'jam_kepulangan', 'durasi_hari', 'pembebanan_biaya',
        'moda_transportasi', 'moda_transportasi_pulang', 'jenis_tiket', 'kelas', 'rute', 'detail_maskapai',
        'no_penerbangan', 'bagasi_tambahan', 'transportasi_lokal',
        'opsi_transportasi',
        'kelas_pulang', 'kota_asal_pulang', 'kode_asal_pulang', 'kode_tujuan_pulang', 'rute_pulang',
        'detail_maskapai_pulang', 'no_penerbangan_pulang', 'bagasi_tambahan_pulang',
        'transportasi_lokal_pulang', 'opsi_transportasi_pulang',
        'hotel_reservasi', 'nama_hotel', 'lokasi_hotel', 'alamat_hotel',
        'check_in', 'check_out', 'jumlah_kamar', 'permintaan_khusus',
        'notes', 'status', 'approval_token','closing_status',
        'closed_at',
        'closing_note',
        'approved_at', 'rejected_at',
        'corporate_status', 'corporate_approved_at', 'corporate_approved_by', 'corporate_note',
    ];

    protected $casts = [
        'opsi_transportasi' => 'array',
        'opsi_transportasi_pulang' => 'array',
        'ttl' => 'date',
        'exp_date_paspor' => 'date',
        'tanggal_keberangkatan' => 'date',
        'check_in' => 'date',
        'check_out' => 'date',
        'bagasi_tambahan' => 'boolean',
        'transportasi_lokal' => 'boolean',
        'bagasi_tambahan_pulang' => 'boolean',
        'transportasi_lokal_pulang' => 'boolean',
        'hotel_reservasi' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'corporate_approved_at' => 'datetime',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke master lokasi berdasarkan kode
    public function originLocation()
    {
        return $this->belongsTo(TransportLocation::class, 'kode_asal', 'code');
    }

    public function destinationLocation()
    {
        return $this->belongsTo(TransportLocation::class, 'kode_tujuan', 'code');
    }

    public function returnOriginLocation()
    {
        return $this->belongsTo(TransportLocation::class, 'kode_asal_pulang', 'code');
    }

    public function returnDestinationLocation()
    {
        return $this->belongsTo(TransportLocation::class, 'kode_tujuan_pulang', 'code');
    }

    public function isCorporateApproved()
    {
        return $this->corporate_status === self::CORP_APPROVED;
    }

    public function scopeCorporatePending($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
                     ->where('corporate_status', self::CORP_PENDING);
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
