<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga\GaRequest;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use App\Services\GaWorkflowService;
use App\Services\GaPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GaExport;

class GaAdminController extends Controller
{
    private function getSharedData()
    {
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $seopage = Seosetting::first();
        $page = Pages::all();
        return compact('title', 'footertext', 'seopage', 'page');
    }

    private function adminFlags()
    {
        $user = auth()->user();

        $isSuperadmin = $user->hasRole('superadmin') || $user->hasRole('Superadmin') || strtolower($user->role ?? '') == 'superadmin';
        $isCorporate = $user->hasRole('corporate') || $user->hasRole('Corporate') || strtolower($user->role ?? '') == 'corporate';
        $isGaManager = !$isSuperadmin && !$isCorporate && $user->hasRole(config('ga.l2_role', 'ga_manager'));
        $isAdmin = !$isSuperadmin && !$isCorporate && !$isGaManager;

        return compact('isSuperadmin', 'isCorporate', 'isGaManager', 'isAdmin');
    }

    private function scopedQuery($user, $flags, $request = null)
    {
        $query = GaRequest::with('items');

        if ($flags['isGaManager']) {
            $query->where('l2_approver_user_id', $user->id);
        }

        if ($request) {
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            if ($request->filled('department') && ($flags['isSuperadmin'] || $flags['isCorporate'])) {
                $query->where('departemen', $request->department);
            }
        }

        return $query;
    }

    public function dashboard()
    {
        $user = auth()->user();
        $flags = $this->adminFlags();

        $base = $this->scopedQuery($user, $flags);

        $total = (clone $base)->count();
        $totalAmount = (clone $base)->sum('total_amount');
        $totalPendingL1 = (clone $base)->where('status', GaRequest::STATUS_PENDING_L1)->count();
        $totalPendingL2 = (clone $base)->where('status', GaRequest::STATUS_PENDING_L2)->count();
        $totalApproved = (clone $base)->where('status', GaRequest::STATUS_APPROVED)->count();
        $totalRejected = (clone $base)->where('status', GaRequest::STATUS_REJECTED)->count();
        $totalExpired = (clone $base)->where('status', GaRequest::STATUS_EXPIRED)->count();
        $totalCancelled = (clone $base)->where('status', GaRequest::STATUS_CANCELLED)->count();

        $recentRequests = $base->orderBy('created_at', 'desc')->limit(5)->get();

        $chartByStatus = GaRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $chartByDepartment = GaRequest::selectRaw('departemen, count(*) as total')
            ->whereNotNull('departemen')
            ->groupBy('departemen')
            ->pluck('total', 'departemen');

        if ($flags['isSuperadmin'] || $flags['isCorporate']) {
            $departments = GaRequest::distinct()->pluck('departemen')->filter();
        } else {
            $departments = collect([]);
        }

        $data = array_merge(
            $this->getSharedData(),
            $flags,
            compact(
                'total', 'totalAmount', 'totalPendingL1', 'totalPendingL2',
                'totalApproved', 'totalRejected', 'totalExpired', 'totalCancelled',
                'recentRequests', 'chartByStatus', 'chartByDepartment', 'departments'
            ),
            ['current_page' => 'ga-dashboard']
        );

        return view('admin.ga.dashboard', $data);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $flags = $this->adminFlags();

        $query = $this->scopedQuery($user, $flags, $request);

        $gaRequests = $query->orderBy('created_at', 'desc')->paginate(20);

        if ($flags['isSuperadmin'] || $flags['isCorporate']) {
            $departments = GaRequest::distinct()->pluck('departemen')->filter();
        } else {
            $departments = collect([]);
        }

        $data = array_merge(
            $this->getSharedData(),
            $flags,
            compact('gaRequests', 'departments'),
            ['current_page' => 'ga-admin']
        );

        return view('admin.ga.index', $data);
    }

    public function show($id)
    {
        $data = $this->getSharedData();
        $gaRequest = GaRequest::with('items')->findOrFail($id);

        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin') || strtolower($user->role ?? '') == 'superadmin';
        $isCorporate = $user->hasRole('corporate') || strtolower($user->role ?? '') == 'corporate';
        $isGaManager = $user->hasRole(config('ga.l2_role', 'ga_manager'));

        $canAct = $isSuperadmin || $isCorporate || ($isGaManager && $gaRequest->l2_approver_user_id == $user->id && $gaRequest->status == GaRequest::STATUS_PENDING_L2);

        $data['gaRequest'] = $gaRequest;
        $data['canAct'] = $canAct;
        $data['current_page'] = 'ga-admin';

        return view('admin.ga.show', $data);
    }

    public function approve($id)
    {
        $gaRequest = GaRequest::findOrFail($id);
        $this->assertCanAct($gaRequest);

        $layer = $gaRequest->status == GaRequest::STATUS_PENDING_L2 ? 'l2' : 'l1';

        app(GaWorkflowService::class)->approve($gaRequest, $layer, auth()->id());

        return redirect()->back()->with('success', 'GA Request ' . $gaRequest->request_no . ' disetujui.');
    }

    public function reject($id, Request $request)
    {
        $gaRequest = GaRequest::findOrFail($id);
        $this->assertCanAct($gaRequest);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        app(GaWorkflowService::class)->reject($gaRequest, auth()->id(), $request->reason);

        return redirect()->back()->with('success', 'GA Request ' . $gaRequest->request_no . ' ditolak.');
    }

    public function downloadPdf($id)
    {
        try {
            $gaRequest = GaRequest::findOrFail($id);
            $pdfService = app(GaPdfService::class);
            $pdf = $pdfService->generate($gaRequest);
            $content = $pdf->output();

            return response($content)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $pdfService->fileName($gaRequest) . '"')
                ->header('Content-Length', strlen($content));
        } catch (\Exception $e) {
            Log::error('Admin GA download PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mendownload PDF.');
        }
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin') || strtolower($user->role ?? '') == 'superadmin';
        $isCorporate = $user->hasRole('corporate') || strtolower($user->role ?? '') == 'corporate';
        $isGaManager = !$isSuperadmin && !$isCorporate && $user->hasRole(config('ga.l2_role', 'ga_manager'));

        $filters = [
            'status' => $request->status,
            'department' => $request->department,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        $userId = $isGaManager ? $user->id : null;

        return Excel::download(new GaExport($filters, $userId), 'ga-requests-report.xlsx');
    }

    private function assertCanAct(GaRequest $gaRequest)
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin') || strtolower($user->role ?? '') == 'superadmin';
        $isCorporate = $user->hasRole('corporate') || strtolower($user->role ?? '') == 'corporate';
        $isGaManager = $user->hasRole(config('ga.l2_role', 'ga_manager'));

        if (!$isSuperadmin && !$isCorporate) {
            if (!$isGaManager || $gaRequest->l2_approver_user_id != $user->id || $gaRequest->status != GaRequest::STATUS_PENDING_L2) {
                abort(403, 'Anda tidak memiliki izin untuk bertindak pada permintaan ini.');
            }
        }

        if (!$gaRequest->isPending()) {
            abort(403, 'GA Request ini tidak dalam status menunggu persetujuan.');
        }
    }
}