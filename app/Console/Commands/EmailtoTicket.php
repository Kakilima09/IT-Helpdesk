<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Ticket\Ticket;
use App\Models\Customer;
use App\Models\User;
use App\Models\EmailTicket;
use App\Models\CustomerSetting;

use Mail;
use App\Mail\mailmailablesend;
use App\Notifications\TicketCreateNotifications;
use App\Models\tickethistory;
use \Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Auth;
use \Webklex\IMAP\Support\AttachmentCollection;
use File;
use Illuminate\Support\Str;
use App\Models\Setting;
use Carbon\Carbon;

class EmailtoTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imap:emailticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        if(setting('IMAP_STATUS') == 'on'){
            $client = User::make([
                'host'          => setting('IMAP_HOST'),
                'port'          => setting('IMAP_PORT'),
                'encryption'    => setting('IMAP_ENCRYPTION'),
                'validate_cert' => true,
                'username'      => setting('IMAP_USERNAME'),
                'password'      => setting('IMAP_PASSWORD'),
                'protocol'      => setting('IMAP_PROTOCOL')
            ]);
            $client->connect();
            $aFolder = $client->getFolders();
            foreach($aFolder as $folder){
                foreach($folder->messages()->unseen()->get() as $message){
                    $userexits = Customer::where('email', $message->getFrom()[0]->mail)->count();
                    if($userexits == 1){
                        $guest = Customer::where('email', $message->getFrom()[0]->mail)->first();

                    }else{
                        $guest = Customer::create([

                            'firstname' => '',
                            'lastname' => '',
                            'username' => $message->getFrom()[0]->personal,
                            'email' => $message->getFrom()[0]->mail,
                            'userType' => 'Guest',
                            'password' => null,
                            'country' => '',
                            'timezone' => 'UTC',
                            'status' => '1',
                            'image' => null,

                        ]);
                        $customersetting = new CustomerSetting();
                        $customersetting->custs_id = $guest->id;
                        $customersetting->save();
                    }
                    $body = $message->getHTMLBody(true);
                    $stripped_body = strip_tags($body);

                    $ticket = Ticket::create([
                        'subject' => $message->getSubject(),
                        'cust_id' => $guest->id,
                        'category_id' => null,
                        'priority' => null,
                        'message' => $stripped_body,
                        'status' => 'New',
                    ]);
                    $ticket = Ticket::find($ticket->id);
                    if($guest->userType == 'Guest'){
                        $ticket->ticket_id = setting('CUSTOMER_TICKETID').'G-'.$ticket->id;
                    }else{
                        $ticket->ticket_id = setting('CUSTOMER_TICKETID').'-'.$ticket->id;
                    }
                     // Auto Overdue Ticket

                    if (setting('AUTO_OVERDUE_TICKET') == 'no') {
                        $ticket->auto_overdue_ticket = null;
                    } else {
                        if (setting('AUTO_OVERDUE_TICKET_TIME') == '0') {
                            $ticket->auto_overdue_ticket = null;
                        } else {
                            if (Auth::guard('customer')->check() && Auth::guard('customer')->user()) {
                                if ($ticket->status == 'Closed') {
                                    $ticket->auto_overdue_ticket = null;
                                } else {
                                    $ticket->auto_overdue_ticket = now()->addDays(setting('AUTO_OVERDUE_TICKET_TIME'));
                                }
                            }
                        }
                    }
                    // Auto Overdue Ticket

                    $finalfile = [];
                    $attachedfiles = $message->getAttachments();
                    foreach($attachedfiles as $attachedfile){
                        array_push($finalfile, $attachedfile->name);
                    }
                    $newArr = implode(",", $finalfile);
                    $ticket->emailticketfile =  $newArr;

                    $message->getAttachments()->each(function ($oAttachment) use ($message) {
                        file_put_contents(public_path('uploads/emailtoticket' . '/' . $oAttachment->name), $oAttachment->content);
                    });

                    $ticket->update();


                    $ticket = Ticket::find($ticket->id);
                    if($ticket->emailticketfile != null){
                        $arraytype = explode(',', $ticket->emailticketfile);
                        foreach($arraytype as $arraytypes){
                            $file_path = public_path('uploads/emailtoticket/'. $arraytypes);
                            $file_size = File::size($file_path) / 1024 / 1024;

                            $attachexten = explode(".", $arraytypes);
                            $appliexten = explode(",", setting('FILE_UPLOAD_TYPES'));
                            $appliextenfinal = Str::remove('.', $appliexten);
                            if(!in_array($attachexten[1], $appliextenfinal) || $file_size > setting('FILE_UPLOAD_MAX') || count($arraytype) > setting('MAX_FILE_UPLOAD')){
                                $ticket->emailticketfile = 'mismatch';
                                $ticket->update();
                                File::delete($file_path);

                            }
                        }
                    }


                    $tickethistory = new tickethistory();
                    $tickethistory->ticket_id = $ticket->id;

                    $output = '<div class="d-flex align-items-center">
                        <div class="mt-0">
                            <p class="mb-0 fs-12 mb-1">Status
                        ';
                    if($ticket->ticketnote->isEmpty()){
                        if($ticket->overduestatus != null){
                            $output .= '
                            <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                            <span class="text-danger font-weight-semibold mx-1">'.$ticket->overduestatus.'</span>
                            ';
                        }else{
                            $output .= '
                            <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                            ';
                        }

                    }else{
                        if($ticket->overduestatus != null){
                            $output .= '
                            <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                            <span class="text-danger font-weight-semibold mx-1">'.$ticket->overduestatus.'</span>
                            <span class="text-warning font-weight-semibold mx-1">Note</span>
                            ';
                        }else{
                            $output .= '
                            <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                            <span class="text-warning font-weight-semibold mx-1">Note</span>
                            ';
                        }
                    }

                    $output .= '
                        <p class="mb-0 fs-17 font-weight-semibold text-dark">Guest User<span class="fs-11 mx-1 text-muted">(Email Ticket)</span></p>
                    </div>
                    <div class="ms-auto">
                    <span class="float-end badge badge-danger-light">
                        <span class="fs-11 font-weight-semibold">' .$guest->userType . ' </span>
                    </span>
                    </div>

                    </div>
                    ';
                    $tickethistory->ticketactions = $output;
                    $tickethistory->save();


                    foreach(User::all() as $user){
                        $user->notify(new TicketCreateNotifications($ticket));
                    }

                    $ticketData = [
                        'ticket_id' => $ticket->ticket_id,
                        'ticket_username' => $ticket->cust->username,
                        'ticket_title' => $ticket->subject,
                        'ticket_file_format' => setting('FILE_UPLOAD_TYPES'),
                        'ticket_file_size' => setting('FILE_UPLOAD_MAX'),
                        'ticket_file_count' => setting('MAX_FILE_UPLOAD'),
                        'ticket_description' => $ticket->message,
                        'ticket_customer_url' => route('guest.ticketdetailshow', $ticket->ticket_id),
                        'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                    ];

                    try{
                        if($ticket->emailticketfile == 'mismatch'){
                            Mail::to($ticket->cust->email)
                            ->send( new mailmailablesend( 'customer_send_guestticket_created_with_attachment_failed', $ticketData ) );
                        }else{
                            Mail::to($ticket->cust->email)
                            ->send( new mailmailablesend( 'customer_send_guestticket_created', $ticketData ) );
                        }

                        foreach(User::all() as $admin){
                            if($admin->getRoleNames()[0] == 'superadmin' && $admin->usetting->emailnotifyon == 1){
                                Mail::to($admin->email)
                                    ->send(new mailmailablesend('admin_send_email_ticket_created', $ticketData));
                            }
                        }

                    }catch(\Exception $e){
                        \Log::error('Failed to send ticket creation email: ' . $e->getMessage());
                    }

                    // Process email replies to existing tickets
                    $this->processEmailReplies($message, $guest);

                    $message->setFlag('SEEN');
                }
            }
        }

        return 0;
    }

    /**
     * Process email replies to existing tickets and send status notifications
     */
    private function processEmailReplies($message, $customer)
    {
        try {
            $subject = $message->getSubject();
            $body = $message->getHTMLBody(true);
            $stripped_body = strip_tags($body);

            // Check if this is a reply to an existing ticket (look for ticket ID in subject)
            $ticketId = $this->extractTicketIdFromSubject($subject);

            if ($ticketId) {
                $ticket = Ticket::where('ticket_id', $ticketId)->first();

                if ($ticket && $ticket->cust_id == $customer->id) {
                    // This is a valid reply to an existing ticket
                    $this->handleTicketReply($ticket, $stripped_body, $message);
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to process email replies: ' . $e->getMessage());
        }
    }

    /**
     * Extract ticket ID from email subject
     */
    private function extractTicketIdFromSubject($subject)
    {
        // Pattern to match ticket IDs like: TICK-123, TICKG-456, etc.
        $pattern = '/(' . preg_quote(setting('CUSTOMER_TICKETID'), '/') . '[A-Z]?-\d+)/i';

        if (preg_match($pattern, $subject, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Handle ticket reply via email
     */
    private function handleTicketReply(Ticket $ticket, $replyMessage, $emailMessage)
    {
        try {
            // Create comment for the reply
            $comment = new \App\Models\Ticket\Comment();
            $comment->ticket_id = $ticket->id;
            $comment->cust_id = $ticket->cust_id;
            $comment->user_id = null; // Customer reply
            $comment->comment = $replyMessage;
            $comment->save();

            // Handle attachments in reply
            $this->handleReplyAttachments($emailMessage, $comment);

            // Update ticket timestamps
            $ticket->last_reply = now();
            $ticket->replystatus = 'Customer-Reply';

            // If ticket was closed, reopen it
            if ($ticket->status == 'Closed') {
                $ticket->status = 'Re-Open';
                $ticket->closedby_user = null;

                // Send reopen notification
                $this->sendTicketStatusEmail($ticket, 'Closed', 'Re-Open', $comment);
            } else {
                $ticket->update();

                // Send reply confirmation to customer
                $this->sendReplyConfirmationEmail($ticket, $comment);
            }

            // Save ticket history
            $this->saveReplyHistory($ticket, $comment);

            // Notify agents about customer reply
            $this->notifyAgentsAboutReply($ticket, $comment);

        } catch (\Exception $e) {
            \Log::error('Failed to handle ticket reply: ' . $e->getMessage());
        }
    }

    /**
     * Handle attachments in email replies
     */
    private function handleReplyAttachments($emailMessage, $comment)
    {
        try {
            $finalfile = [];
            $attachedfiles = $emailMessage->getAttachments();

            foreach($attachedfiles as $attachedfile){
                array_push($finalfile, $attachedfile->name);
            }

            if (!empty($finalfile)) {
                $emailMessage->getAttachments()->each(function ($oAttachment) use ($comment) {
                    $filePath = public_path('uploads/comment/' . $oAttachment->name);
                    file_put_contents($filePath, $oAttachment->content);

                    // Add to comment media collection
                    $comment->addMedia($filePath)->toMediaCollection('comments');
                });
            }

        } catch (\Exception $e) {
            \Log::error('Failed to handle reply attachments: ' . $e->getMessage());
        }
    }

    /**
     * Send reply confirmation email to customer
     */
    private function sendReplyConfirmationEmail(Ticket $ticket, $comment)
    {
        try {
            $ticketData = [
                'ticket_username' => $ticket->cust->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $ticket->status,
                'comment' => $comment->comment,
                'ticket_customer_url' => $ticket->cust->userType == 'Guest'
                    ? route('guest.ticketdetailshow', $ticket->ticket_id)
                    : route('loadmore.load_data', $ticket->ticket_id),
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
            ];

            Mail::to($ticket->cust->email)
                ->send(new mailmailablesend('customer_send_ticket_reply_confirmation', $ticketData));

        } catch (\Exception $e) {
            \Log::error('Failed to send reply confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Save ticket history for reply
     */
    private function saveReplyHistory(Ticket $ticket, $comment)
    {
        try {
            $tickethistory = new tickethistory();
            $tickethistory->ticket_id = $ticket->id;

            $statusHtml = $this->generateStatusHtml($ticket);

            $output = '<div class="d-flex align-items-center">
                <div class="mt-0">
                    <p class="mb-0 fs-12 mb-1">Status' . $statusHtml . '</p>
                    <p class="mb-0 fs-17 font-weight-semibold text-dark">' . $ticket->cust->username . '<span class="fs-11 mx-1 text-muted">(Replied via Email)</span></p>
                </div>
                <div class="ms-auto">
                    <span class="float-end badge badge-info-light">
                        <span class="fs-11 font-weight-semibold">Customer</span>
                    </span>
                </div>
            </div>';

            $tickethistory->ticketactions = $output;
            $tickethistory->save();

        } catch (\Exception $e) {
            \Log::error('Failed to save reply history: ' . $e->getMessage());
        }
    }

    /**
     * Generate status HTML for history
     */
    private function generateStatusHtml(Ticket $ticket)
    {
        $output = '';

        if($ticket->ticketnote->isEmpty()){
            if($ticket->overduestatus != null){
                $output .= '
                <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$ticket->overduestatus.'</span>
                ';
            }else{
                $output .= '
                <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                ';
            }
        }else{
            if($ticket->overduestatus != null){
                $output .= '
                <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$ticket->overduestatus.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }else{
                $output .= '
                <span class="text-burnt-orange font-weight-semibold mx-1">'.$ticket->status.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }
        }

        return $output;
    }

    /**
     * Notify agents about customer reply
     */
    private function notifyAgentsAboutReply(Ticket $ticket, $comment)
    {
        try {
            foreach(User::all() as $user){
                $user->notify(new TicketCreateNotifications($ticket));

                // Send email notification to agents with email notifications enabled
                if($user->usetting && $user->usetting->emailnotifyon == 1){
                    $ticketData = [
                        'ticket_username' => $ticket->cust->username,
                        'ticket_title' => $ticket->subject,
                        'ticket_id' => $ticket->ticket_id,
                        'ticket_status' => $ticket->status,
                        'comment' => $comment->comment,
                        'updated_by' => $ticket->cust->username,
                        'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                    ];

                    Mail::to($user->email)
                        ->send(new mailmailablesend('agent_send_ticket_customer_reply', $ticketData));
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to notify agents about reply: ' . $e->getMessage());
        }
    }

    /**
     * Send ticket status email notification (integrated with MailboxController)
     */
    private function sendTicketStatusEmail(Ticket $ticket, $previousStatus, $newStatus, $comment = null)
    {
        try {
            // Use the MailboxController method if available
            if (class_exists('App\Http\Controllers\Admin\MailboxController')) {
                $mailboxController = new \App\Http\Controllers\Admin\MailboxController();
                return $mailboxController->sendTicketStatusEmail($ticket, $previousStatus, $newStatus, $comment);
            }

            // Fallback implementation
            $customer = $ticket->cust;

            if (!$customer) {
                \Log::error('Customer not found for ticket: ' . $ticket->id);
                return false;
            }

            $ticketData = [
                'ticket_username' => $customer->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $newStatus,
                'previous_status' => $previousStatus,
                'comment' => $comment ? $comment->comment : null,
                'ticket_customer_url' => $customer->userType == 'Guest'
                    ? route('guest.ticketdetailshow', $ticket->ticket_id)
                    : route('loadmore.load_data', $ticket->ticket_id),
                'ticket_admin_url' => url('/admin/ticket-view/' . $ticket->ticket_id),
                'updated_by' => 'System',
            ];

            $emailTemplate = $this->getStatusEmailTemplate($previousStatus, $newStatus);

            Mail::to($customer->email)
                ->send(new mailmailablesend($emailTemplate, $ticketData));

            \Log::info('Ticket status email sent via EmailtoTicket', [
                'ticket_id' => $ticket->ticket_id,
                'from_status' => $previousStatus,
                'to_status' => $newStatus
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Failed to send ticket status email from EmailtoTicket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Determine email template based on status change
     */
    private function getStatusEmailTemplate($previousStatus, $newStatus)
    {
        return match($newStatus) {
            'In Progress' => 'customer_send_ticket_in_progress',
            'On-Hold' => 'customer_send_ticket_on_hold',
            'Solved' => 'customer_send_ticket_solved',
            'Closed' => 'customer_send_ticket_closed',
            'Re-Open' => 'customer_send_ticket_reopen',
            default => 'customer_send_ticket_status_updated'
        };
    }
}
