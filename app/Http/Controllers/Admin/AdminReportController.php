<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use App\Models\User;
use App\Models\Customer;
use App\Models\Ticket\Ticket;
use App\Models\usersettings;
use DB;
use DataTables;
use Carbon\Carbon;
use App\Models\Userrating;
use App\Models\Employeerating;
use App\Models\Ticket\Comment;
use App\Models\Articles\Article;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminReportController extends Controller
{
   public function index(Request $request)
   {
      $this->authorize('Reports Access');

      $users = User::latest('updated_at')->paginate(6);
      $data['users'] = $users;

      $title = Apptitle::first();
      $data['title'] = $title;

      $footertext = Footertext::first();
      $data['footertext'] = $footertext;

      $seopage = Seosetting::first();
      $data['seopage'] = $seopage;

      $post = Pages::all();
      $data['page'] = $post;

      $agentactivec = User::where('status','1')->count();
      $data['agentactivec'] = $agentactivec;
      $agentinactive = User::where('status','0')->count();
      $data['agentinactive'] = $agentinactive;

      $customeractive = Customer::where('status','1')->count();
      $data['customeractive'] = $customeractive;
      $customerinactive = Customer::where('status','0')->count();
      $data['customerinactive'] = $customerinactive;

      $newticket = Ticket::where('status', 'New')->count();
      $data['newticket'] = $newticket;

      $closedticket = Ticket::where('status', 'Closed')->count();
      $data['closedticket'] = $closedticket;

      $inprogressticket = Ticket::where('status', 'Inprogress')->count();
      $data['inprogressticket'] = $inprogressticket;

      $onholdticket = Ticket::where('status', 'On-Hold')->count();
      $data['onholdticket'] = $onholdticket;

      $reopenticket = Ticket::where('status', 'Re-Open')->count();
      $data['reopenticket'] = $reopenticket;

      $prioritylow = Ticket::where('priority', 'Low')->count();
      $data['prioritylow'] = $prioritylow;

      $priorityhigh = Ticket::where('priority', 'High')->count();
      $data['priorityhigh'] = $priorityhigh;

      $prioritymedium = Ticket::where('priority', 'Medium')->count();
      $data['prioritymedium'] = $prioritymedium;

      $prioritycritical = Ticket::where('priority', 'Critical')->count();
      $data['prioritycritical'] = $prioritycritical;

      $articlepublished = Article::where('status', 'Published')->count();
      $data['articlepublished'] = $articlepublished;

      $articleunpublished = Article::where('status', 'UnPublished')->count();
      $data['articleunpublished'] = $articleunpublished;

      // Get filtered tickets for the table
      $ticketQuery = $this->applyTicketFilters($request);
      $tickets = $ticketQuery->orderBy('created_at', 'desc')->paginate(10);
      $data['tickets'] = $tickets;

      // Get employees for filter dropdown
      $employees = User::where('status', '1')->get();
      $data['employees'] = $employees;

      // Get monthly statistics for export
      $monthlyStats = $this->getMonthlyStats();
      $data['monthlyStats'] = $monthlyStats;

      // Get current year and month for dropdowns
      $data['currentYear'] = Carbon::now()->year;
      $data['currentMonth'] = Carbon::now()->month;
      $data['years'] = range(2020, $data['currentYear']);
      $data['months'] = [
          1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
          5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
          9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
      ];

      return view('admin.reports.index')->with($data);
   }

   /**
    * Apply filters to ticket query
    */
   private function applyTicketFilters(Request $request)
   {
      $query = Ticket::with('employee');

      // Filter by start date
      if ($request->filled('start_date')) {
         try {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay();
            $query->whereDate('created_at', '>=', $startDate);
         } catch (\Exception $e) {
            // Invalid date format, ignore filter
         }
      }

      // Filter by end date
      if ($request->filled('end_date')) {
         try {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay();
            $query->whereDate('created_at', '<=', $endDate);
         } catch (\Exception $e) {
            // Invalid date format, ignore filter
         }
      }

      // Filter by status
      if ($request->filled('status') && $request->status != '') {
         $query->where('status', $request->status);
      }

      // Filter by priority
      if ($request->filled('priority') && $request->priority != '') {
         $query->where('priority', $request->priority);
      }

      // Filter by employee
      if ($request->filled('employee_id') && $request->employee_id != '') {
         $query->where('employee_id', $request->employee_id);
      }

      // Filter by ticket ID
      if ($request->filled('ticket_id')) {
         $query->where('ticket_id', 'LIKE', '%' . $request->ticket_id . '%');
      }

      return $query;
   }

   /**
    * Get monthly ticket statistics
    */
   private function getMonthlyStats()
   {
      $currentYear = Carbon::now()->year;
      $monthlyStats = [];

      for ($month = 1; $month <= 12; $month++) {
         $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
         $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();

         $monthlyStats[$month] = [
            'month_name' => $startDate->format('F Y'),
            'total_tickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_tickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->where('status', 'New')->count(),
            'closed_tickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->where('status', 'Closed')->count(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
         ];
      }

      return $monthlyStats;
   }

   public function ticketreports()
   {
      $users = User::get();
      $data['users'] = $users;

      $title = Apptitle::first();
      $data['title'] = $title;

      $footertext = Footertext::first();
      $data['footertext'] = $footertext;

      $seopage = Seosetting::first();
      $data['seopage'] = $seopage;

      $post = Pages::all();
      $data['page'] = $post;

      return view('admin.reports.ticketratingreport')->with($data);
   }

   public function employeedetails($id)
   {
      $users = User::find($id);
      $data['users'] = $users;

      $employeerating = Ticket::select('tickets.*')->leftJoin('comments','comments.ticket_id','tickets.id')
      ->where('comments.user_id', $users->id)->distinct('comments.ticket_id', 'tickets.id')->get();
      $data['employeeratings'] = $employeerating;

      $title = Apptitle::first();
      $data['title'] = $title;

      $footertext = Footertext::first();
      $data['footertext'] = $footertext;

      $seopage = Seosetting::first();
      $data['seopage'] = $seopage;

      $post = Pages::all();
      $data['page'] = $post;

      return view('admin.reports.ratingview')->with($data);
   }

   public function ratingticketdelete($id)
   {
      $ticketratingdelete = Userrating::where('ticket_id', $id)->first();

      $employeeratingdelete = Employeerating::where('urating_id', $ticketratingdelete->id)->get();
      foreach($employeeratingdelete as $employeesrating)
      {
         $employeesrating->delete();
      }
      $ticketratingdelete->delete();

      return response()->json(['success' => 'Delete successfully']);
   }
   
   /**
     * Get monthly statistics for specific year
     */
   private function getMonthlyStatsForYear($year)
   {
      $monthlyStats = [];

      for ($month = 1; $month <= 12; $month++) {
         $startDate = Carbon::create($year, $month, 1)->startOfMonth();
         $endDate = Carbon::create($year, $month, 1)->endOfMonth();

         $monthlyStats[$month] = [
               'month_name' => $startDate->format('F Y'),
               'total_tickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->count(),
               'new_tickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->where('status', 'New')->count(),
               'closed_tickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->where('status', 'Closed')->count(),
               'start_date' => $startDate->format('Y-m-d'),
               'end_date' => $endDate->format('Y-m-d')
         ];
      }

      return $monthlyStats;
   }
   
   /**
 * Export tickets to Excel with filters
 */
   public function exportTickets(Request $request)
   {
      try {
         // Apply filters to export
         $ticketQuery = $this->applyTicketFilters($request);
         $tickets = $ticketQuery->with(['cust', 'selfAssignUser', 'category', 'subcategoriess'])
               ->orderBy('created_at', 'desc')
               ->get();
         
         // Get filter info for filename
         $filterInfo = '';
         if ($request->filled('start_date')) {
               $filterInfo .= '-' . $request->start_date;
         }
         if ($request->filled('end_date')) {
               $filterInfo .= '-to-' . $request->end_date;
         }
         if ($request->filled('status')) {
               $filterInfo .= '-' . $request->status;
         }
         if ($request->filled('priority')) {
               $filterInfo .= '-' . $request->priority;
         }
         
         $filename = 'tickets-report' . $filterInfo . '-' . date('Y-m-d-H-i-s') . '.xlsx';
         
         // Pass filtered tickets and filters to export
         return Excel::download(new TicketsExport($tickets, $request->all()), $filename);
      } catch (\Exception $e) {
         \Log::error('Export error: ' . $e->getMessage());
         return redirect()->back()->with('error', 'Error exporting tickets: ' . $e->getMessage());
      }
   }

    /**
     * API endpoint for getting filtered tickets data (for AJAX)
     */
   public function getFilteredTickets(Request $request)
   {
      try {
         $ticketQuery = $this->applyTicketFilters($request);
         $tickets = $ticketQuery->with('employee')
               ->orderBy('created_at', 'desc')
               ->paginate(10);
         
         return response()->json([
               'success' => true,
               'data' => $tickets
         ]);
      } catch (\Exception $e) {
         return response()->json([
               'success' => false,
               'message' => $e->getMessage()
         ], 500);
      }
   }
}