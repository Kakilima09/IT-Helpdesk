<?php

namespace App\Exports;

use App\Models\Ga\GaRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $userId;

    public function __construct($filters = [], $userId = null)
    {
        $this->filters = $filters;
        $this->userId = $userId;
    }

    public function collection()
    {
        $query = GaRequest::with('items');

        if (!empty($this->userId)) {
            $query->where('l2_approver_user_id', $this->userId);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
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
            'Request No',
            'Pemohon',
            'Departemen',
            'Jml Item',
            'Total Barang',
            'Total Jasa',
            'Total Amount',
            'Harga Maks Barang',
            'Perlu Layer 2',
            'Status',
            'Approver L1',
            'Approver L2',
            'Tanggal Pengajuan',
            'Tanggal Approved',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $timezone = config('app.timezone', 'Asia/Jakarta');

        return [
            $no,
            $row->request_no,
            $row->nama_lengkap,
            $row->departemen ?? '-',
            $row->items->count(),
            $row->goods_total ?? 0,
            $row->services_total ?? 0,
            $row->total_amount,
            $row->max_goods_price ?? 0,
            $row->needs_layer2 ? 'Ya' : 'Tidak',
            ucwords(str_replace('_', ' ', $row->status)),
            $row->l1_approver_name ?? '-',
            $row->l2_approver_name ?? '-',
            $row->created_at ? $row->created_at->setTimezone($timezone)->format('d-m-Y H:i') : '-',
            $row->approved_at ? $row->approved_at->setTimezone($timezone)->format('d-m-Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}