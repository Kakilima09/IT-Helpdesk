<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Ticket\Comment;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Category;
use App\Models\User;
use App\Models\Customer;
use App\Models\Ratingtoken;
use App\Models\CCMAILS;
use App\Models\tickethistory;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Notifications\TicketCreateNotifications;
use App\Mail\mailmailablesend;
use Mail;

class CommentsController extends Controller
{
    /**
     * Post comment and update ticket status
     */
    public function postComment(Request $request, $ticket_id)
    {
        Log::info('Starting postComment', [
            'ticket_id' => $ticket_id,
            'status' => $request->input('status'),
            'user_id' => Auth::id()
        ]);

        try {
            $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();
            $previousStatus = $ticket->status;
            $newStatus = $request->input('status');

            Log::info('Ticket found', [
                'ticket_id' => $ticket->ticket_id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'customer_email' => $ticket->cust->email ?? 'No customer email'
            ]);

            if ($newStatus == 'Solved') {
                return $this->handleSolvedTicket($request, $ticket, $previousStatus);
            } else {
                return $this->handleOtherStatus($request, $ticket, $previousStatus, $newStatus);
            }

        } catch (\Exception $e) {
            Log::error('Error in postComment', [
                'ticket_id' => $ticket_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with("error", lang('An error occurred while processing your request.', 'alerts'));
        }
    }

    /**
     * Handle Solved status (which closes the ticket)
     */
    private function handleSolvedTicket(Request $request, Ticket $ticket, $previousStatus)
    {
        Log::info('Handling Solved ticket', ['ticket_id' => $ticket->ticket_id]);

        // Validate input
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:1',
            'comments.*' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for Solved ticket', [
                'ticket_id' => $ticket->ticket_id,
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create comment
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'cust_id' => null,
            'comment' => $request->input('comment')
        ]);

        Log::info('Comment created for Solved ticket', [
            'comment_id' => $comment->id,
            'ticket_id' => $ticket->id
        ]);

        // Handle media upload
        $this->handleMediaUpload($request, $comment);

        // Update ticket status to Closed
        $this->updateTicketToClosed($ticket, $request);

        // Save ticket history
        $this->saveTicketHistory($ticket, $comment, 'Closed');

        // Send notifications
        $this->sendTicketNotifications($ticket);

        // Create rating token
        $ratingtoken = $this->createRatingToken($ticket);

        // Send emails
        $this->sendClosedTicketEmails($ticket, $comment, $ratingtoken, $request, $previousStatus);

        Log::info('Solved ticket processing completed', ['ticket_id' => $ticket->ticket_id]);

        return redirect()->back()->with("success", lang('The response to the ticket was successful.', 'alerts'));
    }

    /**
     * Handle other statuses (In Progress, On-Hold, etc.)
     */
    private function handleOtherStatus(Request $request, Ticket $ticket, $previousStatus, $newStatus)
    {
        Log::info('Handling Other Status', [
            'ticket_id' => $ticket->ticket_id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus
        ]);

        // Validate input
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:1',
            'comments.*' => 'sometimes|string',
            'note' => 'required_if:status,On-Hold|string|nullable'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for Other Status', [
                'ticket_id' => $ticket->ticket_id,
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update previous comments display
        $this->updatePreviousCommentsDisplay($ticket);

        // Create new comment
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'cust_id' => null,
            'comment' => $request->input('comment'),
            'display' => 1,
        ]);

        Log::info('Comment created for Other Status', [
            'comment_id' => $comment->id,
            'ticket_id' => $ticket->id
        ]);

        // Handle media upload
        $this->handleMediaUpload($request, $comment);

        // Update ticket
        $this->updateTicketStatus($ticket, $request, $newStatus);

        // Save ticket history
        $this->saveTicketHistory($ticket, $comment, 'Responded');

        // Send notifications
        $this->sendTicketNotifications($ticket);

        // Send status update emails
        $this->sendStatusUpdateEmails($ticket, $comment, $previousStatus, $newStatus);

        Log::info('Other Status processing completed', ['ticket_id' => $ticket->ticket_id]);

        return redirect()->back()->with("success", lang('The response to the ticket was successful.', 'alerts'));
    }

    /**
     * Handle media upload for comments
     */
    private function handleMediaUpload(Request $request, Comment $comment)
    {
        try {
            if ($request->has('comments') && is_array($request->input('comments'))) {
                foreach ($request->input('comments', []) as $file) {
                    $filePath = public_path('uploads/comment/' . $file);
                    if (file_exists($filePath)) {
                        $comment->addMedia($filePath)->toMediaCollection('comments');
                        Log::info('Media added to comment', [
                            'comment_id' => $comment->id,
                            'file' => $file
                        ]);
                    } else {
                        Log::warning('File not found for media upload', ['file' => $file]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling media upload', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update ticket to closed status
     */
    private function updateTicketToClosed(Ticket $ticket, Request $request)
    {
        $ticket->status = 'Closed';
        $ticket->replystatus = $request->input('status');
        $ticket->auto_close_ticket = null;
        $ticket->auto_replystatus = null;
        $ticket->last_reply = now();
        $ticket->closing_ticket = now();
        $ticket->auto_overdue_ticket = null;
        $ticket->overduestatus = null;
        $ticket->closedby_user = Auth::id();
        $ticket->lastreply_mail = Auth::id();
        $ticket->update();

        Log::info('Ticket updated to Closed', ['ticket_id' => $ticket->ticket_id]);
    }

    /**
     * Update ticket status for other statuses
     */
    private function updateTicketStatus(Ticket $ticket, Request $request, $newStatus)
    {
        $ticket->status = $newStatus;
        $ticket->replystatus = 'Waiting';

        if ($newStatus == 'On-Hold') {
            $ticket->note = $request->input('note');
            $ticket->auto_close_ticket = null;
            $ticket->auto_replystatus = null;
            $ticket->auto_overdue_ticket = null;
            $ticket->overduestatus = null;
        } else {
            $this->handleAutoTicketSettings($ticket);
        }

        $ticket->last_reply = now();
        $ticket->lastreply_mail = Auth::id();
        $ticket->update();

        Log::info('Ticket status updated', [
            'ticket_id' => $ticket->ticket_id,
            'new_status' => $newStatus
        ]);
    }

    /**
     * Handle auto ticket settings
     */
    private function handleAutoTicketSettings(Ticket $ticket)
    {
        // Auto Close Ticket
        if (setting('AUTO_CLOSE_TICKET') == 'no') {
            $ticket->auto_close_ticket = null;
        } else {
            if (setting('AUTO_CLOSE_TICKET_TIME') == '0') {
                $ticket->auto_close_ticket = null;
            } else {
                if ($ticket->status == 'Closed') {
                    $ticket->auto_close_ticket = null;
                } else {
                    $ticket->auto_close_ticket = now()->addHours(setting('AUTO_RESPONSETIME_TICKET_TIME'))->addDays(setting('AUTO_CLOSE_TICKET_TIME'));
                }
            }
        }

        // Auto Response Ticket
        if (setting('AUTO_RESPONSETIME_TICKET') == 'no') {
            $ticket->auto_replystatus = null;
        } else {
            if (setting('AUTO_RESPONSETIME_TICKET_TIME') == '0') {
                $ticket->auto_replystatus = null;
            } else {
                $ticket->auto_replystatus = now()->addHours(setting('AUTO_RESPONSETIME_TICKET_TIME'));
            }
        }

        // Auto Overdue Ticket
        if (setting('AUTO_OVERDUE_TICKET') == 'no') {
            $ticket->auto_overdue_ticket = null;
            $ticket->overduestatus = null;
        } else {
            if (setting('AUTO_OVERDUE_TICKET_TIME') == '0') {
                $ticket->auto_overdue_ticket = null;
                $ticket->overduestatus = null;
            } else {
                if ($ticket->status == 'Closed') {
                    $ticket->auto_overdue_ticket = null;
                    $ticket->overduestatus = null;
                } else {
                    $ticket->auto_overdue_ticket = null;
                    $ticket->overduestatus = null;
                }
            }
        }
    }

    /**
     * Update previous comments display
     */
    private function updatePreviousCommentsDisplay(Ticket $ticket)
    {
        if ($ticket->comments()->exists()) {
            $ticket->comments()->update(['display' => null]);
            Log::info('Previous comments display updated', ['ticket_id' => $ticket->ticket_id]);
        }
    }

    /**
     * Save ticket history
     */
    private function saveTicketHistory(Ticket $ticket, Comment $comment, $actionType)
    {
        try {
            $tickethistory = new tickethistory();
            $tickethistory->ticket_id = $ticket->id;

            $statusHtml = $this->generateStatusHtml($ticket);

            $actionText = match($actionType) {
                'Closed' => '(Closed)',
                'Responded' => '(Responded)',
                default => '(Updated)'
            };

            $output = '<div class="d-flex align-items-center">
                <div class="mt-0">
                    <p class="mb-0 fs-12 mb-1">Status' . $statusHtml . '</p>
                    <p class="mb-0 fs-17 font-weight-semibold text-dark">' . $comment->user->name . '<span class="fs-11 mx-1 text-muted">' . $actionText . '</span></p>
                </div>
                <div class="ms-auto">
                    <span class="float-end badge badge-primary-light">
                        <span class="fs-11 font-weight-semibold">' . ($comment->user->getRoleNames()[0] ?? 'User') . '</span>
                    </span>
                </div>
            </div>';

            $tickethistory->ticketactions = $output;
            $tickethistory->save();

            Log::info('Ticket history saved', [
                'ticket_id' => $ticket->ticket_id,
                'action_type' => $actionType
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving ticket history', [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate status HTML for history
     */
    private function generateStatusHtml(Ticket $ticket)
    {
        $output = '';

        if ($ticket->ticketnote->isEmpty()) {
            if ($ticket->overduestatus != null) {
                $output .= '
                <span class="' . ($ticket->status == 'On-Hold' ? 'text-warning' : 'text-info') . ' font-weight-semibold mx-1">' . $ticket->status . '</span>
                <span class="text-danger font-weight-semibold mx-1">' . $ticket->overduestatus . '</span>
                ';
            } else {
                $output .= '
                <span class="' . ($ticket->status == 'On-Hold' ? 'text-warning' : 'text-info') . ' font-weight-semibold mx-1">' . $ticket->status . '</span>
                ';
            }
        } else {
            if ($ticket->overduestatus != null) {
                $output .= '
                <span class="' . ($ticket->status == 'On-Hold' ? 'text-warning' : 'text-info') . ' font-weight-semibold mx-1">' . $ticket->status . '</span>
                <span class="text-danger font-weight-semibold mx-1">' . $ticket->overduestatus . '</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            } else {
                $output .= '
                <span class="' . ($ticket->status == 'On-Hold' ? 'text-warning' : 'text-info') . ' font-weight-semibold mx-1">' . $ticket->status . '</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }
        }

        return $output;
    }

    /**
     * Send ticket notifications
     */
    private function sendTicketNotifications(Ticket $ticket)
    {
        try {
            // Notify customer
            $cust = Customer::find($ticket->cust_id);
            if ($cust) {
                $cust->notify(new TicketCreateNotifications($ticket));
                Log::info('Customer notification sent', [
                    'ticket_id' => $ticket->ticket_id,
                    'customer_id' => $cust->id
                ]);
            }

            // Notify users based on category
            if ($ticket->category) {
                $notificationcat = $ticket->category->groupscategoryc()->get();
                $icc = [];

                if ($notificationcat->isNotEmpty()) {
                    foreach ($notificationcat as $igc) {
                        foreach ($igc->groupsc->groupsuser()->get() as $user) {
                            $icc[] = $user->users_id;
                        }
                    }

                    if (empty($icc)) {
                        $admins = User::leftJoin('groups_users', 'groups_users.users_id', 'users.id')
                            ->whereNull('groups_users.groups_id')
                            ->whereNull('groups_users.users_id')
                            ->get();

                        foreach ($admins as $admin) {
                            $admin->notify(new TicketCreateNotifications($ticket));
                        }
                    } else {
                        $users = User::whereIn('id', $icc)->get();
                        foreach ($users as $user) {
                            $user->notify(new TicketCreateNotifications($ticket));
                        }

                        $admins = User::leftJoin('groups_users', 'groups_users.users_id', 'users.id')
                            ->whereNull('groups_users.groups_id')
                            ->whereNull('groups_users.users_id')
                            ->get();

                        foreach ($admins as $admin) {
                            if ($admin->getRoleNames()[0] == 'superadmin') {
                                $admin->notify(new TicketCreateNotifications($ticket));
                            }
                        }
                    }
                } else {
                    $admins = User::leftJoin('groups_users', 'groups_users.users_id', 'users.id')
                        ->whereNull('groups_users.groups_id')
                        ->whereNull('groups_users.users_id')
                        ->get();

                    foreach ($admins as $admin) {
                        $admin->notify(new TicketCreateNotifications($ticket));
                    }
                }
            }

            // Notify all admins if no category
            if (!$ticket->category) {
                $admins = User::get();
                foreach ($admins as $admin) {
                    $admin->notify(new TicketCreateNotifications($ticket));
                }
            }

            Log::info('All notifications sent successfully', ['ticket_id' => $ticket->ticket_id]);

        } catch (\Exception $e) {
            Log::error('Error sending notifications', [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create rating token
     */
    private function createRatingToken(Ticket $ticket)
    {
        $ratingtoken = Ratingtoken::create([
            'token' => str_random(64),
            'ticket_id' => $ticket->id,
        ]);

        Log::info('Rating token created', [
            'ticket_id' => $ticket->ticket_id,
            'token_id' => $ratingtoken->id
        ]);

        return $ratingtoken;
    }

    /**
     * Send status update emails
     */
    private function sendStatusUpdateEmails(Ticket $ticket, Comment $comment, $previousStatus, $newStatus)
    {
        Log::info('Sending status update emails', [
            'ticket_id' => $ticket->ticket_id,
            'from_status' => $previousStatus,
            'to_status' => $newStatus,
            'customer_email' => $ticket->cust->email ?? 'No email'
        ]);

        // Always send to customer first
        $customerEmailSent = $this->sendCustomerStatusEmail($ticket, $comment, $previousStatus, $newStatus);

        // Then send to agents
        $agentEmailSent = $this->sendAgentStatusEmail($ticket, $comment, $previousStatus, $newStatus);

        Log::info('Status update email results', [
            'ticket_id' => $ticket->ticket_id,
            'customer_email_sent' => $customerEmailSent,
            'agent_email_sent' => $agentEmailSent
        ]);
    }

    /**
     * Send email to customer about status update
     */
    private function sendCustomerStatusEmail(Ticket $ticket, Comment $comment, $previousStatus, $newStatus)
    {
        try {
            $customer = $ticket->cust;

            if (!$customer || !$customer->email) {
                Log::error('Customer or customer email not found for status email', [
                    'ticket_id' => $ticket->ticket_id,
                    'customer_id' => $ticket->cust_id
                ]);
                return false;
            }

            $ticketData = [
                'ticket_username' => $customer->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $newStatus,
                'previous_status' => $previousStatus,
                'comment' => $comment->comment,
                'agent_name' => Auth::user()->name,
                'agent_role' => Auth::user()->getRoleNames()[0] ?? 'Agent',
                'ticket_customer_url' => $this->getCustomerTicketUrl($ticket),
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            $emailTemplate = $this->getCustomerStatusEmailTemplate($newStatus);

            Log::info('Sending customer status email', [
                'to' => $customer->email,
                'template' => $emailTemplate,
                'ticket_id' => $ticket->ticket_id,
                'data' => $ticketData
            ]);

            Mail::to($customer->email)
                ->send(new mailmailablesend($emailTemplate, $ticketData));

            Log::info('Customer status email sent successfully', [
                'ticket_id' => $ticket->ticket_id,
                'customer_email' => $customer->email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send customer status email: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send email to agents about status update
     */
    private function sendAgentStatusEmail(Ticket $ticket, Comment $comment, $previousStatus, $newStatus)
    {
        try {
            $agents = $this->getAssignedAgents($ticket);

            if ($agents->isEmpty()) {
                Log::info('No assigned agents found for ticket', ['ticket_id' => $ticket->ticket_id]);
                return false;
            }

            $ticketData = [
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $newStatus,
                'previous_status' => $previousStatus,
                'comment' => $comment->comment,
                'customer_name' => $ticket->cust->username,
                'updated_by' => Auth::user()->name,
                'updated_by_role' => Auth::user()->getRoleNames()[0] ?? 'Agent',
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
            ];

            $sentCount = 0;
            foreach ($agents as $agent) {
                if ($agent->usetting && $agent->usetting->emailnotifyon == 1) {
                    try {
                        Mail::to($agent->email)
                            ->send(new mailmailablesend('send_mail_to_agent_when_ticket_status_updated', $ticketData));
                        $sentCount++;
                        Log::info('Agent status email sent', [
                            'agent' => $agent->email,
                            'ticket_id' => $ticket->ticket_id
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send email to agent: ' . $agent->email, [
                            'error' => $e->getMessage(),
                            'ticket_id' => $ticket->ticket_id
                        ]);
                    }
                }
            }

            Log::info('Agent status emails sent', [
                'count' => $sentCount,
                'total_agents' => $agents->count(),
                'ticket_id' => $ticket->ticket_id
            ]);

            return $sentCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send agent status email: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id
            ]);
            return false;
        }
    }

    /**
     * Send closed ticket emails
     */
    private function sendClosedTicketEmails(Ticket $ticket, Comment $comment, Ratingtoken $ratingtoken, Request $request, $previousStatus)
    {
        Log::info('Sending closed ticket emails', ['ticket_id' => $ticket->ticket_id]);

        // Always send to customer first
        $customerEmailSent = $this->sendCustomerClosedEmail($ticket, $comment, $ratingtoken, $request, $previousStatus);

        // Then send to agents
        $agentEmailSent = $this->sendAgentClosedEmail($ticket, $comment);

        Log::info('Closed ticket email results', [
            'ticket_id' => $ticket->ticket_id,
            'customer_email_sent' => $customerEmailSent,
            'agent_email_sent' => $agentEmailSent
        ]);
    }

    /**
     * Send closed email to customer
     */
    private function sendCustomerClosedEmail(Ticket $ticket, Comment $comment, Ratingtoken $ratingtoken, Request $request, $previousStatus)
    {
        try {
            $closed_agent = User::findOrFail(Auth::id());
            $customer = $ticket->cust;

            if (!$customer || !$customer->email) {
                Log::error('Customer not found for closed ticket email', [
                    'ticket_id' => $ticket->ticket_id,
                    'customer_id' => $ticket->cust_id
                ]);
                return false;
            }

            $ticketData = [
                'closed_agent_name' => $closed_agent->name,
                'closed_agent_role' => $closed_agent->getRoleNames()[0] ?? 'Agent',
                'ticket_username' => $customer->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $ticket->status,
                'previous_status' => $previousStatus,
                'comment' => $comment->comment,
                'ratinglink' => route('guest.rating', $ratingtoken->token),
                'ticket_customer_url' => $this->getCustomerTicketUrl($ticket),
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                'closed_at' => now()->format('Y-m-d H:i:s'),
            ];

            $emailTemplate = $request->rating_on_off ?
                'send_mail_to_customer_when_ticket_closed_by_admin' :
                'customer_rating';

            Log::info('Sending closed ticket email to customer', [
                'to' => $customer->email,
                'template' => $emailTemplate,
                'ticket_id' => $ticket->ticket_id,
                'rating_on_off' => $request->rating_on_off
            ]);

            Mail::to($customer->email)
                ->send(new mailmailablesend($emailTemplate, $ticketData));

            Log::info('Closed ticket email sent to customer successfully', [
                'ticket_id' => $ticket->ticket_id,
                'customer_email' => $customer->email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send closed ticket email to customer: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send closed email to agents
     */
    private function sendAgentClosedEmail(Ticket $ticket, Comment $comment)
    {
        try {
            $agents = $this->getAssignedAgents($ticket);
            $closed_agent = User::findOrFail(Auth::id());

            $ticketData = [
                'closed_agent_name' => $closed_agent->name,
                'closed_agent_role' => $closed_agent->getRoleNames()[0] ?? 'Agent',
                'ticket_username' => $ticket->cust->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'comment' => $comment->comment,
                'ticket_status' => $ticket->status,
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                'closed_at' => now()->format('Y-m-d H:i:s'),
            ];

            $sentCount = 0;
            foreach ($agents as $agent) {
                if ($agent->usetting && $agent->usetting->emailnotifyon == 1) {
                    try {
                        Mail::to($agent->email)
                            ->send(new mailmailablesend('send_mail_to_agent_when_ticket_closed_by_admin_or_agent', $ticketData));
                        $sentCount++;
                        Log::info('Closed ticket email sent to agent', [
                            'agent' => $agent->email,
                            'ticket_id' => $ticket->ticket_id
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send closed email to agent: ' . $agent->email, [
                            'error' => $e->getMessage(),
                            'ticket_id' => $ticket->ticket_id
                        ]);
                    }
                }
            }

            Log::info('Closed ticket agent emails sent', [
                'count' => $sentCount,
                'ticket_id' => $ticket->ticket_id
            ]);

            return $sentCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send agent closed email: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id
            ]);
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

        // If still no agents, get all admin users
        if ($agents->isEmpty()) {
            $agents = User::whereHas('roles', function($query) {
                $query->where('name', 'superadmin')
                      ->orWhere('name', 'admin')
                      ->orWhere('name', 'agent');
            })->get();
        }

        Log::info('Assigned agents retrieved', [
            'ticket_id' => $ticket->ticket_id,
            'agent_count' => $agents->count()
        ]);

        return $agents->unique('id');
    }

    /**
     * Send email notification when ticket is assigned
     */
    public function sendAssignmentEmail(Ticket $ticket, $assignedTo, $assigner)
    {
        Log::info('Sending assignment email', [
            'ticket_id' => $ticket->ticket_id,
            'assigned_to' => $assignedTo->email,
            'assigner' => $assigner->name
        ]);

        try {
            // Email to assigned agent
            $agentTicketData = [
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $ticket->status,
                'assigned_by' => $assigner->name,
                'assigned_by_role' => $assigner->getRoleNames()[0] ?? 'Agent',
                'customer_name' => $ticket->cust->username,
                'ticket_description' => $ticket->message,
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                'assigned_at' => now()->format('Y-m-d H:i:s'),
            ];

            // Send to assigned agent if they have email notifications enabled
            if ($assignedTo->usetting && $assignedTo->usetting->emailnotifyon == 1) {
                Mail::to($assignedTo->email)
                    ->send(new mailmailablesend('send_mail_to_agent_when_ticket_assigned', $agentTicketData));
                
                Log::info('Assignment email sent to agent', [
                    'agent_email' => $assignedTo->email,
                    'ticket_id' => $ticket->ticket_id
                ]);
            }

            // Email to customer about assignment
            $customerTicketData = [
                'ticket_username' => $ticket->cust->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $ticket->status,
                'assigned_agent' => $assignedTo->name,
                'assigned_agent_role' => $assignedTo->getRoleNames()[0] ?? 'Agent',
                'ticket_customer_url' => $this->getCustomerTicketUrl($ticket),
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                'assigned_at' => now()->format('Y-m-d H:i:s'),
            ];

            // Send to customer
            Mail::to($ticket->cust->email)
                ->send(new mailmailablesend('customer_send_ticket_assigned', $customerTicketData));

            Log::info('Assignment email sent to customer', [
                'customer_email' => $ticket->cust->email,
                'ticket_id' => $ticket->ticket_id
            ]);

            // Save assignment history
            $this->saveAssignmentHistory($ticket, $assignedTo, $assigner);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send assignment email: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Save assignment history
     */
    private function saveAssignmentHistory(Ticket $ticket, $assignedTo, $assigner)
    {
        try {
            $tickethistory = new tickethistory();
            $tickethistory->ticket_id = $ticket->id;

            $statusHtml = $this->generateStatusHtml($ticket);

            $output = '<div class="d-flex align-items-center">
                <div class="mt-0">
                    <p class="mb-0 fs-12 mb-1">Status' . $statusHtml . '</p>
                    <p class="mb-0 fs-17 font-weight-semibold text-dark">' . $assigner->name . 
                    '<span class="fs-11 mx-1 text-muted">(Assigned to ' . $assignedTo->name . ')</span></p>
                </div>
                <div class="ms-auto">
                    <span class="float-end badge badge-info-light">
                        <span class="fs-11 font-weight-semibold">' . ($assigner->getRoleNames()[0] ?? 'User') . '</span>
                    </span>
                </div>
            </div>';

            $tickethistory->ticketactions = $output;
            $tickethistory->save();

            Log::info('Assignment history saved', [
                'ticket_id' => $ticket->ticket_id,
                'assigned_to' => $assignedTo->name
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving assignment history', [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send email when multiple agents are assigned to a ticket
     */
    public function sendMultipleAssignmentEmail(Ticket $ticket, $assignedAgents, $assigner)
    {
        Log::info('Sending multiple assignment emails', [
            'ticket_id' => $ticket->ticket_id,
            'assigned_agents_count' => count($assignedAgents),
            'assigner' => $assigner->name
        ]);

        try {
            $successCount = 0;

            foreach ($assignedAgents as $agent) {
                $agentTicketData = [
                    'ticket_title' => $ticket->subject,
                    'ticket_id' => $ticket->ticket_id,
                    'ticket_status' => $ticket->status,
                    'assigned_by' => $assigner->name,
                    'assigned_by_role' => $assigner->getRoleNames()[0] ?? 'Agent',
                    'customer_name' => $ticket->cust->username,
                    'assigned_agents' => $assignedAgents->pluck('name')->implode(', '),
                    'ticket_description' => $ticket->message,
                    'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                    'assigned_at' => now()->format('Y-m-d H:i:s'),
                ];

                if ($agent->usetting && $agent->usetting->emailnotifyon == 1) {
                    Mail::to($agent->email)
                        ->send(new mailmailablesend('send_mail_to_agent_when_ticket_assigned', $agentTicketData));
                    
                    $successCount++;
                    Log::info('Multiple assignment email sent to agent', [
                        'agent_email' => $agent->email,
                        'ticket_id' => $ticket->ticket_id
                    ]);
                }
            }

            // Email to customer about multiple assignments
            $customerTicketData = [
                'ticket_username' => $ticket->cust->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $ticket->status,
                'assigned_agents' => $assignedAgents->pluck('name')->implode(', '),
                'assigned_by' => $assigner->name,
                'ticket_customer_url' => $this->getCustomerTicketUrl($ticket),
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                'assigned_at' => now()->format('Y-m-d H:i:s'),
            ];

            Mail::to($ticket->cust->email)
                ->send(new mailmailablesend('customer_send_ticket_assigned_multiple', $customerTicketData));

            Log::info('Multiple assignment emails completed', [
                'ticket_id' => $ticket->ticket_id,
                'success_count' => $successCount,
                'total_agents' => count($assignedAgents)
            ]);

            // Save multiple assignment history
            $this->saveMultipleAssignmentHistory($ticket, $assignedAgents, $assigner);

            return $successCount;

        } catch (\Exception $e) {
            Log::error('Failed to send multiple assignment emails: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id
            ]);
            return 0;
        }
    }

    /**
     * Save multiple assignment history
     */
    private function saveMultipleAssignmentHistory(Ticket $ticket, $assignedAgents, $assigner)
    {
        try {
            $tickethistory = new tickethistory();
            $tickethistory->ticket_id = $ticket->id;

            $statusHtml = $this->generateStatusHtml($ticket);
            $agentNames = $assignedAgents->pluck('name')->implode(', ');

            $output = '<div class="d-flex align-items-center">
                <div class="mt-0">
                    <p class="mb-0 fs-12 mb-1">Status' . $statusHtml . '</p>
                    <p class="mb-0 fs-17 font-weight-semibold text-dark">' . $assigner->name . 
                    '<span class="fs-11 mx-1 text-muted">(Assigned to multiple agents: ' . $agentNames . ')</span></p>
                </div>
                <div class="ms-auto">
                    <span class="float-end badge badge-info-light">
                        <span class="fs-11 font-weight-semibold">' . ($assigner->getRoleNames()[0] ?? 'User') . '</span>
                    </span>
                </div>
            </div>';

            $tickethistory->ticketactions = $output;
            $tickethistory->save();

        } catch (\Exception $e) {
            Log::error('Error saving multiple assignment history', [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getMessage()
            ]);
        }
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
     * Determine customer email template based on status
     */
    private function getCustomerStatusEmailTemplate($status)
    {
        return match($status) {
            'In Progress' => 'customer_send_ticket_in_progress',
            'On-Hold' => 'customer_send_ticket_on_hold',
            'Solved' => 'customer_send_ticket_solved',
            'Closed' => 'customer_send_ticket_closed',
            'Re-Open' => 'customer_send_ticket_reopen',
            default => 'customer_send_ticket_status_updated'
        };
    }

    /**
     * Store media for comments
     */
    public function storeMedia(Request $request)
    {
        $path = public_path('uploads/comment');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = $file->getClientOriginalName();

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Update comment or ticket message
     */
    public function updateedit(Request $request, $id)
    {
        if ($request->has('message')) {
            $this->validate($request, [
                'message' => 'required'
            ]);
            $ticket = Ticket::findOrFail($id);
            $ticket->message = $request->input('message');
            $ticket->update();
            return redirect()->back();
        } else {
            $this->validate($request, [
                'editcomment' => 'required'
            ]);
            $comment = Comment::findOrFail($id);
            $comment->comment = $request->input('editcomment');
            $comment->update();

            $this->saveCommentHistory($comment, 'Comment Modified');

            return redirect()->back();
        }
    }

    /**
     * Delete comment
     */
    public function deletecomment(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        $this->saveCommentHistory($comment, 'Comment Deleted');

        return response()->json(['success' => lang('The ticket comment has been deleted successfully.', 'alerts')]);
    }

    /**
     * Save comment history
     */
    private function saveCommentHistory(Comment $comment, $action)
    {
        try {
            $tickethistory = new tickethistory();
            $tickethistory->ticket_id = $comment->ticket->id;

            $statusHtml = $this->generateStatusHtml($comment->ticket);

            $output = '<div class="d-flex align-items-center">
                <div class="mt-0">
                    <p class="mb-0 fs-12 mb-1">Status' . $statusHtml . '</p>
                    <p class="mb-0 fs-17 font-weight-semibold text-dark">' . $comment->user->name . '<span class="fs-11 mx-1 text-muted">(' . $action . ')</span></p>
                </div>
                <div class="ms-auto">
                    <span class="float-end badge badge-primary-light">
                        <span class="fs-11 font-weight-semibold">' . ($comment->user->getRoleNames()[0] ?? 'User') . '</span>
                    </span>
                </div>
            </div>';

            $tickethistory->ticketactions = $output;
            $tickethistory->save();

        } catch (\Exception $e) {
            Log::error('Error saving comment history', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete media
     */
    public function imagedestroy($id)
    {
        $commentss = Media::findOrFail($id);
        $commentss->delete();
        return response()->json([
            'success' => lang('Deleted Successfully', 'alerts')
        ]);
    }

    /**
     * Reopen ticket
     */
    public function reopenticket(Request $req)
    {
        try {
            $reopenticket = Ticket::find($req->reopenid);
            if (!$reopenticket) {
                return response()->json([
                    'success' => false,
                    'message' => lang('Ticket not found.', 'alerts')
                ], 404);
            }

            $previousStatus = $reopenticket->status;

            $reopenticket->status = 'Re-Open';
            $reopenticket->replystatus = null;
            $reopenticket->closedby_user = null;
            $reopenticket->lastreply_mail = Auth::id();
            $reopenticket->update();

            $this->saveReopenHistory($reopenticket);

            $cust = Customer::with('custsetting')->find($reopenticket->cust_id);
            if ($cust) {
                $cust->notify(new TicketCreateNotifications($reopenticket));
            }

            $this->sendReopenEmails($reopenticket, $previousStatus);

            return response()->json([
                'success' => true,
                'message' => lang('The ticket has been successfully reopened.', 'alerts'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error reopening ticket', [
                'ticket_id' => $req->reopenid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => lang('An error occurred while reopening the ticket.', 'alerts')
            ], 500);
        }
    }

    /**
     * Save reopen history
     */
    private function saveReopenHistory(Ticket $ticket)
    {
        try {
            $tickethistory = new tickethistory();
            $tickethistory->ticket_id = $ticket->id;

            $statusHtml = $this->generateStatusHtml($ticket);

            $output = '<div class="d-flex align-items-center">
                <div class="mt-0">
                    <p class="mb-0 fs-12 mb-1">Status' . $statusHtml . '</p>
                    <p class="mb-0 fs-17 font-weight-semibold text-dark">' . Auth::user()->name . '<span class="fs-11 mx-1 text-muted">(Re-opened)</span></p>
                </div>
                <div class="ms-auto">
                    <span class="float-end badge badge-primary-light">
                        <span class="fs-11 font-weight-semibold">' . (Auth::user()->getRoleNames()[0] ?? 'User') . '</span>
                    </span>
                </div>
            </div>';

            $tickethistory->ticketactions = $output;
            $tickethistory->save();

        } catch (\Exception $e) {
            Log::error('Error saving reopen history', [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send reopen emails
     */
    private function sendReopenEmails(Ticket $ticket, $previousStatus)
    {
        try {
            $ccemailsend = CCMAILS::where('ticket_id', $ticket->id)->first();

            $ticketData = [
                'ticket_username' => $ticket->cust->username,
                'ticket_title' => $ticket->subject,
                'ticket_id' => $ticket->ticket_id,
                'ticket_status' => $ticket->status,
                'previous_status' => $previousStatus,
                'reopened_by' => Auth::user()->name,
                'ticket_customer_url' => $ticket->cust->userType == 'Guest'
                    ? route('gusetticket', $ticket->ticket_id)
                    : route('loadmore.load_data', $ticket->ticket_id),
                'ticket_admin_url' => url('/admin/ticket-view/'.$ticket->ticket_id),
                'reopened_at' => now()->format('Y-m-d H:i:s'),
            ];

            // Send to customer
            Mail::to($ticket->cust->email)
                ->send(new mailmailablesend('customer_send_ticket_reopen', $ticketData));

            // Send to CC emails if any
            if ($ccemailsend && !empty($ccemailsend->ccemails)) {
                Mail::to($ccemailsend->ccemails)
                    ->send(new mailmailablesend('customer_send_ticket_reopen', $ticketData));
            }

            // Send to agents
            $this->sendAgentStatusEmail($ticket, null, $previousStatus, 'Re-Open');

            Log::info('Reopen emails sent successfully', ['ticket_id' => $ticket->ticket_id]);

        } catch (\Exception $e) {
            Log::error('Failed to send reopen emails: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id
            ]);
        }
    }

    /**
     * Destroy method (placeholder)
     */
    public function destroy($ticket_id)
    {
        // Implementation for ticket deletion
    }
}
