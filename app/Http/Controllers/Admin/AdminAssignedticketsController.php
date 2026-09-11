<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ticket\Ticket;
use App\Models\User;
use Auth;
use App\Models\tickethistory;

use Mail;
use App\Mail\mailmailablesend;
use App\Notifications\TicketAssignNotification;

class AdminAssignedticketsController extends Controller
{

    public function create(Request $request)
    {
        $this->validate($request, [
            'assigned_user_id' => 'required',
        ]);

        $calID = Ticket::find($request->assigned_id);
        
        // Check if user is self-assigning
        $isSelfAssign = in_array(Auth::id(), $request->assigned_user_id);
        
        if ($isSelfAssign) {
            $calID->selfassignuser_id = Auth::id();
            $calID->myassignuser_id = null;
        } else {
            $calID->myassignuser_id = Auth::id();
            $calID->selfassignuser_id = null;
        }
        
        $calID->save();

        $calID->ticketassignmutliple()->sync($request->assigned_user_id);

        // user information
        $users = User::whereIn('id', $request->assigned_user_id)->get();
        $useroutput = '';
        foreach($users as $user)
        {
            $useroutput .= '
            <div class="fs-11 font-weight-semibold ps-3">
                <div>
                    <span class="fs-12">'.$user->name.'</span>
                    <span class="text-muted">(Assignee)</span>
                </div>
                <small class="text-muted useroutput" >'.$user->getRoleNames()[0].'</small>
            </div>
            ';
        }

        // Assignee
        $tickethistory = new tickethistory();
        $tickethistory->ticket_id = $calID->id;

        $output = '<div class="d-flex align-items-center">
            <div class="mt-0">
                <p class="mb-0 fs-12 mb-1">Status
            ';
        
        if($calID->ticketnote->isEmpty()){
            if($calID->overduestatus != null){
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$calID->overduestatus.'</span>
                ';
            }else{
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                ';
            }
        }else{
            if($calID->overduestatus != null){
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$calID->overduestatus.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }else{
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }
        }

        $assignType = $isSelfAssign ? 'Self-Assigned' : 'Assigned';
        $output .= '
            <p class="mb-0 fs-17 font-weight-semibold text-dark">'.Auth::user()->name.'<span class="fs-11 mx-1 text-muted">('.$assignType.')</span></p>
            '. $useroutput .'
        </div>
            <div class="ms-auto">
                <span class="float-end badge badge-primary-light">
                    <span class="fs-11 font-weight-semibold">'.Auth::user()->getRoleNames()[0].'</span>
                </span>
            </div>
        </div>
        ';
        
        $tickethistory->ticketactions = $output;
        $tickethistory->save();

        // Send notifications to assigned agents
        try{
            $assignee = $calID->ticketassignmutliples;
            foreach($assignee as $assignees){
                $user = User::where('id',$assignees->toassignuser_id)->first();
                if($user){
                    // Send notification
                    $user->notify(new TicketAssignNotification($calID));
                    
                    // Send email to assigned agent
                    if($user->usetting && $user->usetting->emailnotifyon == 1){
                        $ticketData = [
                            'ticket_username' => $user->name,
                            'ticket_id' => $calID->ticket_id,
                            'ticket_title' => $calID->subject,
                            'ticket_description' => $calID->message,
                            'ticket_status' => $calID->status,
                            'ticket_priority' => $calID->priority,
                            'assigned_by' => Auth::user()->name,
                            'ticket_admin_url' => url('/admin/ticket-view/'.$calID->ticket_id),
                            'assigned_date' => now()->format('F j, Y \a\t g:i A'),
                        ];

                        Mail::to($user->email)
                            ->send(new mailmailablesend('when_ticket_assign_to_other_employee', $ticketData));
                    }
                }
            }

            // Send email to ticket creator (customer)
            $ticketCreator = $calID->cust;
            if ($ticketCreator && $ticketCreator->email) {
                $customerTicketData = [
                    'ticket_username' => $ticketCreator->username,
                    'ticket_id' => $calID->ticket_id,
                    'ticket_title' => $calID->subject,
                    'ticket_description' => $calID->message,
                    'ticket_status' => $calID->status,
                    'assigned_agent' => Auth::user()->name,
                    'assigned_date' => now()->format('F j, Y \a\t g:i A'),
                    'ticket_customer_url' => route('guestticket', $calID->ticket_id),
                ];

                Mail::to($ticketCreator->email)
                    ->send(new mailmailablesend('when_ticket_assigned_to_agent', $customerTicketData));
            }

        }catch(\Exception $e){
            // Log the error for debugging
            \Log::error('Email sending failed: ' . $e->getMessage());
            return response()->json(['code'=>200, 'success'=> lang('The ticket was successfully assigned but email notification failed.', 'alerts')], 200);
        }

        return response()->json(['code'=>200, 'success'=> lang('The ticket was successfully assigned.', 'alerts')], 200);
    }

    public function show(Request $req, $id){
        if($req->ajax())
        {
            $output = '';

            $assign = Ticket::find($id);
            $assugnuser_id = $assign->ticketassignmutliples->pluck('toassignuser_id')->toArray();

            $data = User::get();

            $total_row = $data->count();

            if($total_row > 0){
                $output .='<option label="Select Agent"></option>';
                foreach($data as $row){
                    if(Auth::user()->id != $row->id){
                        $output .= '
                        <option  value="'.$row->id.'"' .(in_array($row->id, $assugnuser_id)? 'selected': '').  '>'.$row->name.' ('.(!empty($row->getRoleNames()[0])? $row->getRoleNames()[0] : '').')</option>
                        ';
                    } else {
                        // Add current user for self-assign option
                        $output .= '
                        <option  value="'.$row->id.'"' .(in_array($row->id, $assugnuser_id)? 'selected': '').  '>'.$row->name.' ('.(!empty($row->getRoleNames()[0])? $row->getRoleNames()[0] : '').') - Self</option>
                        ';
                    }
                }
            }
            
            $data = array(
                'assign_data'=> $assign,
                'table_data' => $output,
                'total_data' => $total_row
            );

            return response()->json($data);
        }
    }

    public function update(Request $req, $id)
    {
        $calID = Ticket::find($id);
        $calID->myassignuser_id = null;
        $calID->selfassignuser_id = null;
        $calID->save();
        $calID->ticketassignmutliple()->detach($req->assigned_userid);

        $tickethistory = new tickethistory();
        $tickethistory->ticket_id = $calID->id;

        $output = '<div class="d-flex align-items-center">
            <div class="mt-0">
                <p class="mb-0 fs-12 mb-1">Status
            ';
        if($calID->ticketnote->isEmpty()){
            if($calID->overduestatus != null){
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$calID->overduestatus.'</span>
                ';
            }else{
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                ';
            }
        }else{
            if($calID->overduestatus != null){
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                <span class="text-danger font-weight-semibold mx-1">'.$calID->overduestatus.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }else{
                $output .= '
                <span class="text-teal font-weight-semibold mx-1">'.$calID->status.'</span>
                <span class="text-warning font-weight-semibold mx-1">Note</span>
                ';
            }
        }

        $output .= '
            <p class="mb-0 fs-17 font-weight-semibold text-dark">'.Auth::user()->name.'<span class="fs-11 mx-1 text-muted">(UnAssigned Ticket)</span></p>
        </div>
        <div class="ms-auto">
        <span class="float-end badge badge-primary-light">
            <span class="fs-11 font-weight-semibold">'.Auth::user()->getRoleNames()[0].'</span>
        </span>
        </div>
        </div>
        ';
        
        $tickethistory->ticketactions = $output;
        $tickethistory->save();

        return response()->json(['data'=> $calID, 'success'=> lang('Updated successfully', 'alerts')]);
    }
}