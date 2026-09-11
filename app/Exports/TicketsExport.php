<?php

namespace App\Exports;

use App\Models\Ticket\Ticket;
use App\Models\Customer;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Database\Eloquent\Collection;

class TicketsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|null
     */
    protected $tickets;

    /**
     * @var array|null
     */
    protected $filters;

    /**
     * Constructor untuk menerima data tiket yang sudah difilter
     * 
     * @param \Illuminate\Database\Eloquent\Collection|null $tickets
     * @param array|null $filters
     */
    public function __construct($tickets = null, $filters = null)
    {
        $this->tickets = $tickets;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Jika ada data tiket yang dikirim (sudah difilter), gunakan data tersebut
        if ($this->tickets instanceof Collection) {
            return $this->tickets;
        }
        
        // Jika $this->tickets adalah query builder result
        if ($this->tickets && !($this->tickets instanceof Collection)) {
            return collect($this->tickets);
        }
        
        // Jika tidak ada filter, ambil semua data
        return Ticket::with(['cust', 'selfAssignUser', 'category', 'subcategoriess'])->get();
    }

    /**
     * Define the headings for the Excel sheet
     */
    public function headings(): array
    {
        $headings = [
            'Ticket ID',
            'Subject',
            'Description',
            'User',
            'Customer Email',
            'Assigned Ticket (Agent)',
            'Category',
            'Subcategory',
            'Priority',
            'Status',
            'Created Date',
            'Updated Date',
            'Last Reply Date'
        ];

        // Tambahkan informasi filter di heading jika ada (opsional)
        if ($this->filters && $this->hasActiveFilters()) {
            $filterInfo = $this->getFilterInfo();
            $headings[] = 'Filter Info';
        }

        return $headings;
    }

    /**
     * Map the data for each ticket dengan format yang benar
     */
    public function map($ticket): array
    {
        // 1. Data Customer - menggunakan nama dari table user
        $customerName = 'N/A';
        $customerEmail = 'N/A';
        
        if ($ticket->cust) {
            $customerName = $ticket->cust->firstname ?? 'N/A';
            $customerEmail = $ticket->cust->email ?? 'N/A';
        } else {
            // Fallback: cari customer berdasarkan cust_id di table users
            $customer = User::find($ticket->cust_id);
            if ($customer) {
                $customerName = $customer->firstname ?? $customer->name ?? 'N/A';
                $customerEmail = $customer->email ?? 'N/A';
            }
        }

        // 2. Data Assigned To - khusus super admin yang assign ticket
        $selfAssignedUserName = 'Unassigned';
        
        if ($ticket->selfassignuser_id) {
            // Jika relationship selfAssignUser sudah diload
            if ($ticket->selfAssignUser) {
                $selfAssignedUserName = $ticket->selfAssignUser->name ?? $ticket->selfAssignUser->username ?? 'Unknown';
            } else {
                // Fallback: cari user berdasarkan selfassignuser_id
                $selfAssignedUser = User::find($ticket->selfassignuser_id);
                if ($selfAssignedUser) {
                    $selfAssignedUserName = $selfAssignedUser->name ?? $selfAssignedUser->username ?? 'Unknown';
                }
            }
        }

        // 3. Data Category
        $categoryName = 'N/A';
        if ($ticket->category) {
            $categoryName = $ticket->category->name ?? 'N/A';
        }

        // 4. Data Subcategory (sesuai yang dibuat oleh user)
        $subcategoryName = 'N/A';
        if ($ticket->subcategoriess) {
            $subcategoryName = $ticket->subcategoriess->subcategoryname ?? 'N/A';
        } elseif ($ticket->subcategories) {
            // Jika ada subcategory ID tapi relationship tidak loaded
            $subcategoryName = 'Subcategory ID: ' . $ticket->subcategories;
        }

        // 5. Format priority dan status
        $priority = $this->formatPriority($ticket->priority);
        $status = $this->formatStatus($ticket->status);

        $data = [
            $ticket->ticket_id ?? 'N/A',
            $ticket->subject ?? 'N/A',
            strip_tags($ticket->message ?? 'No description'),
            $customerName,
            $customerEmail,
            $selfAssignedUserName,
            $categoryName,
            $subcategoryName,
            $priority,
            $status,
            $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : 'N/A',
            $ticket->updated_at ? $ticket->updated_at->format('Y-m-d H:i:s') : 'N/A',
            $ticket->last_reply ? $ticket->last_reply->format('Y-m-d H:i:s') : 'No reply yet'
        ];

        // Tambahkan informasi filter di akhir data (opsional)
        if ($this->filters && $this->hasActiveFilters()) {
            $data[] = $this->getFilterInfo();
        }

        return $data;
    }

    /**
     * Check if there are active filters
     */
    private function hasActiveFilters(): bool
    {
        if (!$this->filters) {
            return false;
        }

        $filterFields = ['start_date', 'end_date', 'status', 'priority', 'employee_id', 'ticket_id'];
        foreach ($filterFields as $field) {
            if (isset($this->filters[$field]) && !empty($this->filters[$field])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get filter information as string
     */
    private function getFilterInfo(): string
    {
        $info = [];
        if (!empty($this->filters['start_date'])) {
            $info[] = 'Start: ' . $this->filters['start_date'];
        }
        if (!empty($this->filters['end_date'])) {
            $info[] = 'End: ' . $this->filters['end_date'];
        }
        if (!empty($this->filters['status'])) {
            $info[] = 'Status: ' . $this->filters['status'];
        }
        if (!empty($this->filters['priority'])) {
            $info[] = 'Priority: ' . $this->filters['priority'];
        }
        if (!empty($this->filters['ticket_id'])) {
            $info[] = 'Ticket ID: ' . $this->filters['ticket_id'];
        }
        
        return implode(' | ', $info);
    }

    /**
     * Format priority value untuk lebih readable
     */
    private function formatPriority($priority)
    {
        $priorityMap = [
            'Low' => 'Low',
            'Medium' => 'Medium',
            'High' => 'High',
            'Critical' => 'Critical',
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];

        return $priorityMap[$priority] ?? $priority ?? 'N/A';
    }

    /**
     * Format status value untuk lebih readable
     */
    private function formatStatus($status)
    {
        $statusMap = [
            'New' => 'New',
            'Inprogress' => 'In Progress',
            'On-Hold' => 'On Hold',
            'Reopen' => 'Reopen',
            'Reopened' => 'Reopened',
            'Closed' => 'Closed',
            'new' => 'New',
            'inprogress' => 'In Progress',
            'on-hold' => 'On Hold',
            'reopen' => 'Reopen',
            'reopened' => 'Reopened',
            'closed' => 'Closed',
        ];

        return $statusMap[$status] ?? $status ?? 'N/A';
    }

    /**
     * Apply styles to the Excel sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings) as bold
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE6E6FA']
                ]
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Ticket ID
            'B' => 25, // Subject
            'C' => 40, // Description
            'D' => 20, // Customer Name
            'E' => 25, // Customer Email
            'F' => 25, // Assigned To (Super Admin)
            'G' => 15, // Category
            'H' => 15, // Subcategory
            'I' => 12, // Priority
            'J' => 12, // Status
            'K' => 18, // Created Date
            'L' => 18, // Updated Date
            'M' => 18, // Last Reply Date
        ];
    }
}