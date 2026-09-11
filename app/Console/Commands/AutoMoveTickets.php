<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Ticket\Ticket;
use Mail;
use App\Mail\mailmailablesend;
use App\Mail\VerifyMail;
use Auth;
use App\Models\Customer;
use App\Notifications\TicketCreateNotifications;
use App\Models\tickethistory;
use Carbon\Carbon;

class AutoMoveTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:automove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically move tickets to in-progress status based on conditions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Cek apakah fitur auto move ticket diaktifkan
        if(setting('AUTO_MOVE_TICKET') == 'yes'){

            $this->info('Starting auto move ticket process...');

            // Ambil tickets yang memenuhi kriteria untuk dipindahkan ke in-progress
            $ticketsToMove = Ticket::whereIn('status', ['New', 'Re-Open'])
                ->where(function($query) {
                    // Kondisi 1: Ticket yang sudah diassign ke agent
                    $query->whereNotNull('assigned_to')
                          ->orWhereHas('replies', function($q) {
                              // Kondisi 2: Ticket yang sudah ada balasan dari agent
                              $q->where('user_id', '!=', DB::raw('cust_id'));
                          })
                          ->orWhere('priority', 'High') // Kondisi 3: Ticket dengan priority High
                          ->orWhere('created_at', '<=', Carbon::now()->subHours(24)); // Kondisi 4: Ticket yang sudah dibuat lebih dari 24 jam
                })
                ->get();

            $movedCount = 0;

            foreach($ticketsToMove as $ticket){
                try {
                    // Simpan status lama untuk history
                    $oldStatus = $ticket->status;

                    // Update status ticket menjadi Inprogress
                    $ticket->status = 'Inprogress';
                    $ticket->updated_at = now();
                    $ticket->save();

                    // Buat history record
                    $this->createTicketHistory($ticket, $oldStatus);

                    // Kirim notifikasi email
                    $this->sendNotificationEmail($ticket);

                    // Kirim notifikasi ke customer
                    $this->sendCustomerNotification($ticket);

                    $movedCount++;
                    $this->info("Ticket {$ticket->ticket_id} moved to Inprogress");

                } catch (\Exception $e) {
                    $this->error("Failed to move ticket {$ticket->ticket_id}: " . $e->getMessage());
                }
            }

            $this->info("Auto move ticket process completed. {$movedCount} tickets moved to Inprogress status.");

        } else {
            $this->info('Auto move ticket feature is disabled.');
        }

        return 0;
    }

    /**
     * Create ticket history record
     */
    private function createTicketHistory($ticket, $oldStatus)
    {
        $tickethistory = new tickethistory();
        $tickethistory->ticket_id = $ticket->id;

        $output = '<div class="d-flex align-items-center">
            <div class="mt-0">
                <p class="mb-0 fs-12 mb-1">Status Changed
            ';

        if($ticket->ticketnote->isEmpty()){
            if($ticket->overduestatus != null){
                $output .= '
                <span class="text-info font-weight-semibold mx-1">'.$oldStatus.'</span>
                <span class="mx-1">→</span>
                <span class="text-success font-weight-semibold mx-1">'.$ticket->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$ticket->overduestatus.'</span>
                ';
            } else {
                $output .= '
                <span class="text-info font-weight-semibold mx-1">'.$oldStatus.'</span>
                <span class="mx-1">→</span>
                <span class="text-success font-weight-semibold mx-1">'.$ticket->status.'</span>
                ';
            }
        } else {
            if($ticket->overduestatus != null){
                $output .= '
                <span class="text-info font-weight-semibold mx-1">'.$oldStatus.'</span>
                <span class="mx-1">→</span>
                <span class="text-success font-weight-semibold mx-1">'.$ticket->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$ticket->overduestatus.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            } else {
                $output .= '
                <span class="text-info font-weight-semibold mx-1">'.$oldStatus.'</span>
                <span class="mx-1">→</span>
                <span class="text-success font-weight-semibold mx-1">'.$ticket->status.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }
        }

        $output .= '
            <p class="mb-0 fs-17 font-weight-semibold text-dark">Auto Moved to In-Progress</p>
            <p class="mb-0 fs-12 text-muted">System automatically moved this ticket to in-progress status</p>
        </div>
        </div>
        ';

        $tickethistory->ticketactions = $output;
        $tickethistory->save();
    }

    /**
     * Send notification email to customer
     */
    private function sendNotificationEmail($ticket)
    {
        $ticketData = [
            'ticket_username' => $ticket->cust->username,
            'ticket_id' => $ticket->ticket_id,
            'ticket_title' => $ticket->subject,
            'ticket_description' => $ticket->message,
            'ticket_status' => $ticket->status,
            'ticket_customer_url' => route('loadmore.load_data', $ticket->ticket_id),
            'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
            'ticket_old_status' => 'Active',
            'ticket_new_status' => 'In Progress',
        ];

        try {
            Mail::to($ticket->cust->email)
                ->send(new mailmailablesend('customer_send_ticket_status_changed', $ticketData));
        } catch (\Exception $e) {
            $this->error("Failed to send email for ticket {$ticket->ticket_id}: " . $e->getMessage());
        }
    }

    /**
     * Send notification to customer
     */
    private function sendCustomerNotification($ticket)
    {
        try {
            $cust = Customer::find($ticket->cust_id);
            if ($cust) {
                $cust->notify(new TicketCreateNotifications($ticket));
            }
        } catch (\Exception $e) {
            $this->error("Failed to send notification for ticket {$ticket->ticket_id}: " . $e->getMessage());
        }
    }

    /**
     * Alternative method for specific condition auto move
     * Bisa dipanggil dari controller atau command lain
     */
    public function moveTicketsBasedOnReply()
    {
        // Cari tickets yang sudah ada balasan dari agent tapi masih status New/Re-Open
        $ticketsWithAgentReplies = Ticket::whereIn('status', ['New', 'Re-Open'])
            ->whereHas('replies', function($query) {
                $query->where('user_id', '!=', DB::raw('cust_id')); // Balasan bukan dari customer
            })
            ->get();

        foreach($ticketsWithAgentReplies as $ticket) {
            $oldStatus = $ticket->status;
            $ticket->status = 'Inprogress';
            $ticket->save();

            $this->createTicketHistory($ticket, $oldStatus);
            $this->info("Ticket {$ticket->ticket_id} moved to Inprogress based on agent reply");
        }
    }

    /**
     * Alternative method for priority-based auto move
     */
    public function moveHighPriorityTickets()
    {
        // Otomatis pindahkan high priority tickets setelah waktu tertentu
        $highPriorityTickets = Ticket::where('priority', 'High')
            ->whereIn('status', ['New', 'Re-Open'])
            ->where('created_at', '<=', Carbon::now()->subHours(2)) // 2 jam setelah dibuat
            ->get();

        foreach($highPriorityTickets as $ticket) {
            $oldStatus = $ticket->status;
            $ticket->status = 'Inprogress';
            $ticket->save();

            $this->createTicketHistory($ticket, $oldStatus);
            $this->info("Ticket {$ticket->ticket_id} moved to Inprogress (High Priority)");
        }
    }
}
