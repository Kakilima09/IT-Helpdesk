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
        $query = PapdRequest::where('status', 'approved');

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
            'Pemohon',
            'ID Karyawan',
            'Tujuan',
            'Pembebanan Biaya',
            'Transportasi / Akomodasi',
            'Tanggal Pengajuan',
            'Status Approve Atasan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        // Transportasi & Akomodasi
        $transportasi = ucfirst($row->moda_transportasi);
        if ($row->kelas) {
            $transportasi .= ' (' . $row->kelas . ')';
        }
        
        $akomodasi = 'Tidak';
        if ($row->hotel_reservasi && $row->nama_hotel) {
            $akomodasi = $row->nama_hotel;
            if ($row->lokasi_hotel) {
                $akomodasi .= ' (' . $row->lokasi_hotel . ')';
            }
        }

        $transportasiAkomodasi = $transportasi;
        if ($row->hotel_reservasi) {
            $transportasiAkomodasi .= ' | Hotel: ' . $akomodasi;
        }

        // Format tanggal dengan timezone yang benar
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $createdAt = $row->created_at ? $row->created_at->setTimezone($timezone)->format('d-m-Y H:i') : '-';

        return [
            $no,
            $row->nama_lengkap,
            $row->id_karyawan,
            $row->kota_tujuan,
            $row->pembebanan_biaya ?? '-',
            $transportasiAkomodasi,
            $createdAt,
            ucfirst($row->status), // Status Approve Atasan (selalu 'Approved' karena query filter)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}