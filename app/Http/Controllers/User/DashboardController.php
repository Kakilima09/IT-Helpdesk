<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Hash;
use AuthenticatesUsers;
use App\Models\User;
use App\Models\Ticket\Ticket;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use DataTables;
use Str;
use App\Models\Announcement;

class DashboardController extends Controller
{

   public function userTickets()
   {
       $user = Auth::guard('customer')->user();

       // Get all tickets for the user
       $tickets = Ticket::where('cust_id', $user->id)->latest('updated_at')->get();

       // Get active tickets (New, Re-Open) - EXCLUDE Inprogress
       $active = Ticket::where('cust_id', $user->id)
           ->whereIn('status', ['New', 'Re-Open'])
           ->get();

       // Get on-progress tickets (Inprogress status)
       $onprogress = Ticket::where('cust_id', $user->id)
           ->where('status', 'Inprogress')
           ->get();

       // Get closed tickets
       $closed = Ticket::where('cust_id', $user->id)
           ->where('status', 'Closed')
           ->get();

       // Get on-hold tickets
       $onhold = Ticket::where('cust_id', $user->id)
           ->where('status', 'On-Hold')
           ->get();

       $data['onhold'] = $onhold;
       $data['onprogress'] = $onprogress; // Tambahkan onprogress ke data

       $title = Apptitle::first();
       $data['title'] = $title;

       $footertext = Footertext::first();
       $data['footertext'] = $footertext;

       $seopage = Seosetting::first();
       $data['seopage'] = $seopage;

       $post = Pages::all();
       $data['page'] = $post;

       $now = now();
       $announcement = announcement::whereDate('enddate', '>=', $now->toDateString())->whereDate('startdate', '<=', $now->toDateString())->get();
       $data['announcement'] = $announcement;

       $announcements = Announcement::whereNotNull('announcementday')->get();
       $data['announcements'] = $announcements;

       return view('user.dashboard', compact('tickets','active','closed','onprogress', 'title','footertext'))->with($data);
   }

   /**
    * Method untuk memindahkan ticket ke status in-progress
    */
   public function moveToInProgress($ticketId)
   {
       try {
           $ticket = Ticket::where('cust_id', Auth::guard('customer')->id())
                           ->where('id', $ticketId)
                           ->firstOrFail();

           // Update status ticket menjadi 'Inprogress'
           $ticket->update([
               'status' => 'Inprogress',
               'updated_at' => now()
           ]);

           return response()->json([
               'success' => true,
               'message' => 'Ticket berhasil dipindahkan ke status In-Progress'
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'success' => false,
               'message' => 'Gagal memindahkan ticket: ' . $e->getMessage()
           ], 500);
       }
   }

   /**
    * Method untuk mendapatkan detail ticket in-progress
    */
   public function getInProgressTickets()
   {
       $inprogressTickets = Ticket::where('cust_id', Auth::guard('customer')->id())
                                   ->where('status', 'Inprogress')
                                   ->with(['assignedTo', 'category']) // Sesuaikan dengan relasi model
                                   ->orderBy('updated_at', 'desc')
                                   ->get();

       return response()->json([
           'success' => true,
           'tickets' => $inprogressTickets
       ]);
   }

   /**
    * Method untuk update status ticket oleh user
    */
   public function updateTicketStatus(Request $request, $ticketId)
   {
       $request->validate([
           'status' => 'required|in:Inprogress,On-Hold,Closed,New,Re-Open'
       ]);

       try {
           $ticket = Ticket::where('cust_id', Auth::guard('customer')->id())
                           ->where('id', $ticketId)
                           ->firstOrFail();

           $ticket->update([
               'status' => $request->status,
               'updated_at' => now()
           ]);

           return response()->json([
               'success' => true,
               'message' => 'Status ticket berhasil diupdate',
               'ticket' => $ticket
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'success' => false,
               'message' => 'Gagal mengupdate status ticket: ' . $e->getMessage()
           ], 500);
       }
   }

   // Method lainnya tetap sama...
   public function notify(){

        $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        $post = Pages::all();
        $data['page'] = $post;

        $notifications = auth()->guard('customer')->user()->notifications()->paginate('10')->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
        });
        $data['notifications'] = $notifications;


        return view('user.notification')->with($data);
   }

   public function markNotification(Request $request)
   {
       auth()->guard('customer')->user()
        ->unreadNotifications
        ->when($request->input('id'), function ($query) use ($request) {
            return $query->where('id', $request->input('id'));
        })
        ->markAsRead();

       return response()->noContent();
   }

   public function Notificationview($id)
   {
        $notification = auth()->guard('customer')->user()->notifications()->where('id', $id)->firstOrFail();
        $data['notifications'] = $notification;

        $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        $post = Pages::all();
        $data['page'] = $post;

        return view('user.notification.viewnotification')->with($data);
   }

   public function notifystatus(Request $request)
   {
       $status = $request->statusnotify;
       if(!$status){
            $notifications = auth()->guard('customer')->user()->notifications()->paginate('10')->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
            });
       }
       else{
            $notifications =  auth()->guard('customer')->user()->notifications()->whereIn('data->status', $status)->paginate('10')->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
            });
       }

       $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

       $view = view('user.notificationpageinclude',compact('notifications','title', 'footertext', 'seopage'))->render();
       return response()->json(['html'=>$view]);

   }

   public function notifysearch(Request $request)
   {
       $status = $request->notifysearch;

       if($status){
            $notifications = auth()->guard('customer')->user()->notifications()->where(function($query){
                $keyword = request()->notifysearch;
                $query->where('data->title','LIKE', "%{$keyword}%")
                ->orWhere('data->ticket_id','LIKE', "%{$keyword}%")
                ->orWhere('data->mailsubject','LIKE', "%{$keyword}%")
                ->orWhere('data->mailtext','LIKE', "%{$keyword}%");
            })->get()->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
            });
       }
       else{
            $notifications =  auth()->guard('customer')->user()->notifications()->paginate()->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
            });
       }

       $title = Apptitle::first();
        $data['title'] = $title;

        $footertext = Footertext::first();
        $data['footertext'] = $footertext;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

       $view = view('user.notificationpageinclude',compact('notifications','title', 'footertext', 'seopage'))->render();
       return response()->json(['html'=>$view]);

   }

   public function notifydelete(Request $request)
   {
       $id = $request->id;

       $notificationsdelete = auth()->guard('customer')->user()->notifications()->find($id);
       $notificationsdelete->delete();

       return response()->json(['success'=> lang('Deleted Successfully', 'alerts'),200]);
   }

}
