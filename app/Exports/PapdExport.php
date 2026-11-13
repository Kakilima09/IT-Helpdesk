<?php

namespace App\Exports;

use App\Models\Papd\PapdRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PapdExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PapdRequest::with(['user', 'corporateApprover'])
            ->where(function ($query) {
                $query->where('status', 'approved')
                    ->orWhere('corporate_status', 'rejected');
            });

        if (!empty($this->filters['department'])) {
            $query->where('departemen', $this->filters['department']);
        }
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID PAPD',
            'Note Status Corporate',
            'Requester',
            'Pemohon',
            'Tanggal Approve Corporate',
            'Detail Transaksi',
            'Beban Biaya',
            'Approved Corporate',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $timezone = config('app.timezone', 'Asia/Jakarta');
        $corporateApprovedAt = $row->corporate_approved_at
            ? $row->corporate_approved_at->setTimezone($timezone)->format('d-m-Y H:i')
            : '-';
        $requester = $this->userDisplayName($row->user);
        $corporateApprover = $this->userDisplayName($row->corporateApprover);

        return [
            $no,
            $row->ticket_number,
            $row->corporate_note ?: '-',
            $requester,
            $row->nama_lengkap,
            $corporateApprovedAt,
            $this->transportDetails($row),
            $row->pembebanan_biaya ?? '-',
            $row->corporate_status === 'approved' ? $corporateApprover : '-',
        ];
    }

    private function userDisplayName($user): string
    {
        if (!$user) {
            return '-';
        }

        $fullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));

        return $user->name ?: ($fullName ?: '-');
    }

    private function transportDetails($row): string
    {
        $ticketType = ($row->jenis_tiket ?? 'sekali_jalan') === 'pulang_pergi'
            ? 'Pulang Pergi'
            : 'Sekali Jalan';
        $details = [
            'Jenis tiket: ' . $ticketType,
            $this->transportLegDetails(
                'Berangkat',
                $row->moda_transportasi,
                $row->kota_asal ?: $row->kode_asal,
                $row->kota_tujuan ?: $row->kode_tujuan,
                $row->kelas,
                $row->detail_maskapai,
                $row->no_penerbangan,
                $row->catatan_berangkat,
                $row->bagasi_tambahan,
                $row->transportasi_lokal,
                $row->opsi_transportasi
            ),
        ];

        if ($ticketType === 'Pulang Pergi') {
            $details[] = $this->transportLegDetails(
                'Pulang',
                $row->moda_transportasi_pulang,
                $row->kota_asal_pulang ?: $row->kode_asal_pulang,
                $row->kota_tujuan_pulang ?: $row->kode_tujuan_pulang,
                $row->kelas_pulang,
                $row->detail_maskapai_pulang,
                $row->no_penerbangan_pulang,
                $row->catatan_pulang,
                $row->bagasi_tambahan_pulang,
                $row->transportasi_lokal_pulang,
                $row->opsi_transportasi_pulang
            );
        }

        return implode(' | ', array_filter($details));
    }

    private function transportLegDetails($label, $mode, $origin, $destination, $class, $operator, $number, $note, $extraBaggage, $localTransport, $options): string
    {
        $details = [$label . ': ' . ucfirst($mode ?? '-')];

        if ($origin || $destination) {
            $details[] = 'Rute: ' . ($origin ?: '-') . ' - ' . ($destination ?: '-');
        }
        if ($class) {
            $details[] = 'Kelas: ' . $class;
        }
        if ($operator) {
            $details[] = 'Operator: ' . $operator;
        }
        if ($number) {
            $details[] = 'Nomor perjalanan: ' . $number;
        }
        if ($extraBaggage !== null) {
            $details[] = 'Bagasi tambahan: ' . ($extraBaggage ? 'Ya' : 'Tidak');
        }
        if ($localTransport !== null) {
            $details[] = 'Transportasi lokal: ' . ($localTransport ? 'Ya' : 'Tidak');
        }
        if (is_array($options) && $options) {
            $details[] = 'Opsi: ' . implode(', ', $options);
        }
        if ($note) {
            $details[] = 'Catatan: ' . $note;
        }

        return implode(', ', $details);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}