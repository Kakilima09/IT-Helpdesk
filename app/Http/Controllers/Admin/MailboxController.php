<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\User;
use App\Models\Customer;
use App\Models\Sendmail;
use App\Models\senduserlist;
use App\Models\Ticket\Ticket;
use Auth;
use Session;
use DataTables;
use App\Notifications\CustomerCustomNotifications;
use App\Mail\AppMailer;
use Str;

use Mail;
use App\Mail\mailmailablesend;


class MailboxController extends Controller
{
    public function index()
    {

        $this->authorize('Custom Notifications Access');

        $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        $customnotify = Sendmail::latest()->get();
        $data['customnotify'] = $customnotify;

        return view('admin.custom-notification.index')->with($data);
    }

    public function customercompose()
    {
        $this->authorize('Custom Notifications Customer');
        $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        $user = Customer::where('userType','Customer')->get();
        $data['users'] = $user;

        return view('admin.custom-notification.customercompose')->with($data);
    }

    public function customercomposesend(Request $request)
    {
        $this->authorize('Custom Notifications Customer');
        $request->validate([
            'message' => 'required|max:60000',
            'subject' => 'required|max:255',
            'users' => 'required',
            'tag' => 'required|max:255',
            'selecttagcolor' => 'required',
        ]);
        $mailsend = new Sendmail();
        $mailsend->user_id = Auth::id();
        $mailsend->mailsubject = $request->input('subject');
        $mailsend->mailtext = $request->input('message');
        $mailsend->tag = $request->input('tag');
        $mailsend->selecttagcolor = $request->input('selecttagcolor');
        $mailsend->save();

        foreach($request->users as $value){
            senduserlist::create([
                'mail_id' => $mailsend->id,
                'tocust_id' => $value,
            ]);
        }

        $cust = Customer::find($request->users);

        foreach($cust as $value){

            $value->notify(new CustomerCustomNotifications($mailsend));
        }

        $ticketData = [
            'notification_subject' => $mailsend->mailsubject,
            'notification_message' => $mailsend->mailtext,
            'notification_tag' => $mailsend->tag,
        ];
        foreach($cust as $value){
            try{

                Mail::to($value->email)
                ->send( new mailmailablesend('when_send_customnotify_email_to_selected_member', $ticketData) );

            }catch(\Exception $e){
                return redirect('admin/customnotification')->with('success', lang('A custom notification was successfully sent to the customer.', 'alerts'));
            }
        }


        return redirect('admin/customnotification')->with('success', lang('A custom notification was successfully sent to the customer.', 'alerts'));

    }

    public function employeecompose()
    {
        $this->authorize('Custom Notifications Employee');
        $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        $user = User::get();
        $data['users'] = $user;

        return view('admin.custom-notification.employeecompose')->with($data);
    }


    public function employeecomposesend(Request $request)
    {
        $this->authorize('Custom Notifications Employee');
        $request->validate([
            'message' => 'required|max:60000',
            'subject' => 'required|max:255',
            'users' => 'required',
            'tag' => 'required|max:255',
            'selecttagcolor' => 'required',
        ]);
        $mailsend = new Sendmail();
        $mailsend->user_id = Auth::id();
        $mailsend->mailsubject = $request->input('subject');
        $mailsend->mailtext = $request->input('message');
        $mailsend->tag = $request->input('tag');
        $mailsend->selecttagcolor = $request->input('selecttagcolor');
        $mailsend->save();

        foreach($request->users as $value){
            senduserlist::create([
                'mail_id' => $mailsend->id,
                'touser_id' => $value,
            ]);
        }
        $cust = User::find($request->users);
        foreach($cust as $value){

            $value->notify(new CustomerCustomNotifications($mailsend));
        }


        $ticketData = [
            'notification_subject' => $mailsend->mailsubject,
            'notification_message' => $mailsend->mailtext,
            'notification_tag' => $mailsend->tag,
        ];
        foreach($cust as $value){
            try{

                if($value->usetting->emailnotifyon == 1){
                    Mail::to($value->email)
                    ->send( new mailmailablesend('when_send_customnotify_email_to_selected_member', $ticketData) );
                }

            }catch(\Exception $e){
                return redirect('admin/customnotification')->with('success', lang('A custom notification was successfully sent to the employee.', 'alerts'));
            }
        }



        return redirect('admin/customnotification')->with('success', lang('A custom notification was successfully sent to the employee.', 'alerts'));


    }

    /**
     * Send automatic email notification to customer when ticket status changes
     */
    public function sendTicketStatusEmail(Ticket $ticket, $previousStatus, $newStatus, $comment = null)
    {
        try {
            $customer = $ticket->cust;

            if (!$customer) {
                \Log::error('Customer not found for ticket: ' . $ticket->id);
                return false;
            }

            // Prepare ticket data for email
            $ticketData = [
                'ticket_username' => $customer->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $newStatus,
                'previous_status' => $previousStatus,
                'comment' => $comment ? $comment->comment : null,
                'ticket_customer_url' => $this->getCustomerTicketUrl($ticket),
                'ticket_admin_url' => url('/admin/ticket-view/' . $ticket->ticket_id),
                'updated_by' => Auth::check() ? Auth::user()->name : 'System',
            ];

            // Determine email template based on status change
            $emailTemplate = $this->getStatusEmailTemplate($previousStatus, $newStatus);

            // Send email to customer
            Mail::to($customer->email)
                ->send(new mailmailablesend($emailTemplate, $ticketData));

            // Send CC emails if any
            $this->sendCCEmails($ticket, $emailTemplate, $ticketData);

            \Log::info('Ticket status email sent successfully', [
                'ticket_id' => $ticket->ticket_id,
                'customer_email' => $customer->email,
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
                'template' => $emailTemplate
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Failed to send ticket status email: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send email notification to assigned agents when ticket status changes
     */
    public function sendAgentStatusEmail(Ticket $ticket, $previousStatus, $newStatus, $comment = null)
    {
        try {
            $agents = $this->getAssignedAgents($ticket);

            if ($agents->isEmpty()) {
                \Log::info('No assigned agents found for ticket: ' . $ticket->ticket_id);
                return false;
            }

            $ticketData = [
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $newStatus,
                'previous_status' => $previousStatus,
                'comment' => $comment ? $comment->comment : null,
                'customer_name' => $ticket->cust->username,
                'updated_by' => Auth::check() ? Auth::user()->name : 'System',
                'ticket_admin_url' => url('/admin/ticket-view/' . $ticket->ticket_id),
            ];

            $emailTemplate = 'send_mail_to_agent_when_ticket_status_updated';

            foreach ($agents as $agent) {
                if ($agent->usetting && $agent->usetting->emailnotifyon == 1) {
                    Mail::to($agent->email)
                        ->send(new mailmailablesend($emailTemplate, $ticketData));
                }
            }

            \Log::info('Agent status email sent successfully', [
                'ticket_id' => $ticket->ticket_id,
                'agent_count' => $agents->count(),
                'status_change' => $previousStatus . ' -> ' . $newStatus
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Failed to send agent status email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get assigned agents for a ticket
     */
    private function getAssignedAgents(Ticket $ticket)
    {
        $agents = collect();

        // Check for multiple assigned users
        if ($ticket->myassignuser && $ticket->ticketassignmutliples) {
            foreach ($ticket->ticketassignmutliples as $assignment) {
                $agent = User::find($assignment->toassignuser_id);
                if ($agent) {
                    $agents->push($agent);
                }
            }
        }

        // Check for self-assigned user
        if ($ticket->selfassignuser_id) {
            $agent = User::find($ticket->selfassignuser_id);
            if ($agent && !$agents->contains('id', $agent->id)) {
                $agents->push($agent);
            }
        }

        // If no specific assignments, get agents from category groups
        if ($agents->isEmpty() && $ticket->category) {
            $notificationCats = $ticket->category->groupscategoryc()->get();
            $agentIds = [];

            foreach ($notificationCats as $notificationCat) {
                foreach ($notificationCat->groupsc->groupsuser()->get() as $user) {
                    $agentIds[] = $user->users_id;
                }
            }

            if (!empty($agentIds)) {
                $categoryAgents = User::whereIn('id', $agentIds)->get();
                $agents = $agents->merge($categoryAgents);
            }
        }

        return $agents->unique('id');
    }

    /**
     * Determine the appropriate email template based on status change
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

    /**
     * Get customer ticket URL based on user type
     */
    private function getCustomerTicketUrl(Ticket $ticket)
    {
        if ($ticket->cust->userType == 'Guest') {
            return route('guest.ticketdetailshow', $ticket->ticket_id);
        } else {
            return route('loadmore.load_data', $ticket->ticket_id);
        }
    }

    /**
     * Send CC emails for ticket status changes
     */
    private function sendCCEmails(Ticket $ticket, $template, $ticketData)
    {
        try {
            // Assuming you have a CCMAILS model for CC emails
            if (class_exists('App\Models\CCMAILS')) {
                $ccemails = \App\Models\CCMAILS::where('ticket_id', $ticket->id)->first();

                if ($ccemails && !empty($ccemails->ccemails)) {
                    Mail::to($ccemails->ccemails)
                        ->send(new mailmailablesend($template, $ticketData));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send CC emails: ' . $e->getMessage());
        }
    }

    /**
     * Bulk send ticket status notifications (for batch processing)
     */
    public function bulkSendStatusNotifications(Request $request)
    {
        $this->authorize('Custom Notifications Access');

        $request->validate([
            'ticket_ids' => 'required|array',
            'status' => 'required|string',
            'message' => 'nullable|string'
        ]);

        $ticketIds = $request->input('ticket_ids');
        $newStatus = $request->input('status');
        $customMessage = $request->input('message');

        $successCount = 0;
        $errorCount = 0;

        foreach ($ticketIds as $ticketId) {
            $ticket = Ticket::where('ticket_id', $ticketId)->first();

            if ($ticket) {
                $previousStatus = $ticket->status;

                // Create a mock comment if custom message provided
                $comment = null;
                if ($customMessage) {
                    $comment = (object) ['comment' => $customMessage];
                }

                $result = $this->sendTicketStatusEmail($ticket, $previousStatus, $newStatus, $comment);

                if ($result) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => lang('Status notifications sent successfully.', 'alerts'),
            'success_count' => $successCount,
            'error_count' => $errorCount
        ]);
    }

    public function mailsent()
    {

        $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        $sendmails = Sendmail::latest()->get();
        $data['sendmails'] = $sendmails;

        return view('admin.custom-notification.sendmail')->with($data);
    }

    public function show($id){
        $this->authorize('Custom Notifications View');
        $custom = Sendmail::find($id);

        return response()->json($custom);
    }

    public function destroy($id)
    {
        $this->authorize('Custom Notifications Delete');
        $customdelete = Sendmail::find($id);
        $customdelete->touser()->delete();
        $customdelete->delete();

      return response()->json(['success'=> lang('"Custom notification" was successfully deleted.', 'alerts')]);
    }

    public function allnotifydelete(Request $request)
    {
        $id_array = $request->input('id');

        $sendmails = Sendmail::whereIn('id', $id_array)->get();

        foreach($sendmails as $sendmail){
            $sendmail->touser()->delete();
            $sendmail->delete();

        }
        return response()->json(['success'=> lang('"Custom notification" was successfully deleted.', 'alerts')]);

    }
}
