<?php

namespace App\Http\Controllers\User\Ga;

use App\Http\Controllers\Controller;
use App\Models\Ga\GaRequest;
use App\Models\Ga\GaRequestItem;
use App\Models\Ga\GaCategory;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use App\Models\Customfield;
use App\Models\Announcement;
use App\Models\User;
use App\Services\GaNotificationService;
use App\Services\GaPdfService;
use App\Services\GaWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class GaRequestController extends Controller
{
    private function getSharedData()
    {
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $seopage = Seosetting::first();
        $page = Pages::all();
        if (!$page instanceof Collection) {
            $page = collect($page);
        }
        $customfields = Customfield::whereIn('displaytypes', ['both', 'createticket'])
                                   ->where('status', '1')
                                   ->get();
        if (!$customfields instanceof Collection) {
            $customfields = collect($customfields);
        }
        $now = now();
        $announcement = Announcement::whereDate('enddate', '>=', $now->toDateString())
                                    ->whereDate('startdate', '<=', $now->toDateString())
                                    ->get();
        if (!$announcement instanceof Collection) {
            $announcement = collect($announcement);
        }
        $announcements = Announcement::whereNotNull('announcementday')->get();
        if (!$announcements instanceof Collection) {
            $announcements = collect($announcements);
        }

        return compact('title', 'footertext', 'seopage', 'page', 'customfields', 'announcement', 'announcements');
    }

    private function resolveRequester()
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return null;
        }

        $user = User::where('email', $customer->email)->first();

        if ($user) {
            return [
                'customer' => $customer,
                'requester_type' => 'user',
                'user_id' => $user->id,
                'customer_id' => null,
                'user' => $user,
            ];
        }

        return [
            'customer' => $customer,
            'requester_type' => 'customer',
            'user_id' => null,
            'customer_id' => $customer->id,
            'user' => null,
        ];
    }

    public function index()
    {
        $requester = $this->resolveRequester();

        $query = GaRequest::query();

        if ($requester['requester_type'] == 'user') {
            $query->where('user_id', $requester['user_id']);
        } else {
            $query->where('customer_id', $requester['customer_id']);
        }

        $gaRequests = $query->orderBy('created_at', 'desc')->get();

        $total = $gaRequests->count();
        $pendingL1 = $gaRequests->where('status', GaRequest::STATUS_PENDING_L1)->count();
        $pendingL2 = $gaRequests->where('status', GaRequest::STATUS_PENDING_L2)->count();
        $approved = $gaRequests->where('status', GaRequest::STATUS_APPROVED)->count();
        $rejected = $gaRequests->where('status', GaRequest::STATUS_REJECTED)->count();
        $expired = $gaRequests->where('status', GaRequest::STATUS_EXPIRED)->count();

        $data = array_merge(
            compact('gaRequests', 'total', 'pendingL1', 'pendingL2', 'approved', 'rejected', 'expired'),
            $this->getSharedData(),
            ['current_page' => 'ga', 'notificationPref' => $requester['user'] ? ($requester['user']->notification_pref ?? 'email') : ($requester['customer']->notification_pref ?? 'email')]
        );

        return view('user.ga.index', $data);
    }

    public function updatePref(Request $request)
    {
        $requester = $this->resolveRequester();

        $request->validate([
            'notification_pref' => 'required|in:email,whatsapp,both',
        ]);

        $pref = $request->input('notification_pref');

        if ($requester['requester_type'] === 'user' && $requester['user']) {
            $requester['user']->notification_pref = $pref;
            $requester['user']->save();
        } else {
            $requester['customer']->notification_pref = $pref;
            $requester['customer']->save();
        }

        return redirect()->route('ga.index')->with('success', 'Preferensi notifikasi GA berhasil diperbarui.');
    }

    public function create()
    {
        $requester = $this->resolveRequester();
        $categories = GaCategory::active()->orderBy('name')->get();

        $data = array_merge(
            compact('categories', 'requester'),
            $this->getSharedData(),
            ['current_page' => 'ga.create']
        );

        return view('user.ga.create', $data);
    }

    public function getUserData(Request $request)
    {
        try {
            $term = $request->input('q', '');
            if (strlen($term) < 2) {
                return response()->json([]);
            }

            $columns = Schema::getColumnListing('users');
            $hasFirstname = in_array('firstname', $columns);
            $hasLastname = in_array('lastname', $columns);
            $hasDepartment = in_array('departments', $columns);

            $query = User::where('name', 'LIKE', '%' . $term . '%')
                        ->orWhere('email', 'LIKE', '%' . $term . '%');
            if ($hasFirstname && $hasLastname) {
                $query->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $term . '%']);
            } elseif ($hasFirstname) {
                $query->orWhere('firstname', 'LIKE', '%' . $term . '%');
            }

            $users = $query->limit(10)->get();

            $results = [];
            foreach ($users as $user) {
                $fullName = $this->fullName($user);
                $results[] = [
                    'id' => $user->id,
                    'text' => $fullName . ' (' . $user->email . ')',
                    'name' => $fullName,
                    'email' => $user->email,
                    'department' => $hasDepartment ? ($user->departments ?? '') : '',
                ];
            }

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Error di ga getUserData: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $requester = $this->resolveRequester();
        $isUserRequester = $requester['requester_type'] == 'user';

        $applicant = $this->applicantData($requester);

        $validator = Validator::make($request->all(), [
            'atasan_user_id' => $isUserRequester ? 'required|exists:users,id' : 'nullable',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:goods,service',
            'items.*.category_id' => 'nullable|exists:ga_categories,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        foreach ($request->items as $i => $item) {
            if ($item['item_type'] == GaRequest::TYPE_GOODS && empty($item['category_id'])) {
                return back()->withErrors(["items.$i.category_id" => 'Kategori (ATK/RTK) wajib dipilih untuk item barang.'])->withInput();
            }
        }

        $maxGoodsPrice = 0;
        $goodsTotal = 0;
        $servicesTotal = 0;

        foreach ($request->items as $i => $item) {
            $amount = (float) $item['qty'] * (float) $item['price'];
            if ($item['item_type'] == GaRequest::TYPE_GOODS) {
                if ((float) $item['price'] > $maxGoodsPrice) {
                    $maxGoodsPrice = (float) $item['price'];
                }
                $goodsTotal += $amount;
            } else {
                $servicesTotal += $amount;
            }
        }

        $needsLayer2 = $maxGoodsPrice > (float) config('ga.threshold', 500000);

        list($l1Id, $l1Name, $l1Email) = app(GaWorkflowService::class)->resolveL1Approver(
            $isUserRequester ? $request->atasan_user_id : null
        );

        if (!$l1Email) {
            return back()->with('error', 'Belum ada approver yang tersedia. Silakan hubungi administrator.' )->withInput();
        }

        $atasanName = null;
        $atasanEmail = null;
        if ($isUserRequester && $request->atasan_user_id) {
            $atasan = User::find($request->atasan_user_id);
            if ($atasan) {
                $atasanName = $this->fullName($atasan);
                $atasanEmail = $atasan->email;
            }
        }

        $gaRequest = GaRequest::create([
            'requester_type' => $requester['requester_type'],
            'user_id' => $requester['user_id'],
            'customer_id' => $requester['customer_id'],
            'nama_lengkap' => $applicant['nama_lengkap'],
            'jabatan' => $applicant['jabatan'],
            'departemen' => $applicant['departemen'],
            'entitas' => $applicant['entitas'],
            'email' => $applicant['email'],
            'no_hp' => $applicant['no_hp'],
            'atasan_user_id' => $isUserRequester ? $request->atasan_user_id : null,
            'atasan_nama' => $atasanName,
            'atasan_email' => $atasanEmail,
            'l1_approver_user_id' => $l1Id,
            'l1_approver_name' => $l1Name,
            'l1_approver_email' => $l1Email,
            'status' => GaRequest::STATUS_PENDING_L1,
            'needs_layer2' => $needsLayer2,
            'max_goods_price' => $maxGoodsPrice,
            'goods_total' => $goodsTotal,
            'services_total' => $servicesTotal,
            'total_amount' => $goodsTotal + $servicesTotal,
            'notes' => $request->notes,
        ]);

        $gaRequest->update([
            'request_no' => 'GA-' . now()->format('Ymd') . '-' . str_pad($gaRequest->id, 4, '0', STR_PAD_LEFT),
        ]);

        foreach ($request->items as $item) {
            GaRequestItem::create([
                'ga_request_id' => $gaRequest->id,
                'item_type' => $item['item_type'],
                'category_id' => ($item['item_type'] == GaRequest::TYPE_GOODS && !empty($item['category_id'])) ? $item['category_id'] : null,
                'name' => $item['name'],
                'qty' => $item['qty'],
                'unit' => $item['unit'] ?? null,
                'price' => $item['price'],
                'amount' => (float) $item['qty'] * (float) $item['price'],
            ]);
        }

        try {
            app(GaWorkflowService::class)->notifyRequester($gaRequest, 'GA Request Dikirim', 'GA Request Anda telah dikirim dan menunggu persetujuan.', GaRequest::STATUS_PENDING_L1);
        } catch (\Exception $e) {
            Log::error('Gagal kirim notifikasi database GA: ' . $e->getMessage());
        }

        try {
            app(GaNotificationService::class)->notifyL1($gaRequest);
        } catch (\Exception $e) {
            Log::error('Gagal kirim notifikasi L1 GA: ' . $e->getMessage());
        }

        return redirect()->route('ga.show', $gaRequest->id)
                         ->with('success', 'GA Request berhasil dikirim, silakan tunggu persetujuan.');
    }

    public function show($id)
    {
        $requester = $this->resolveRequester();

        $query = GaRequest::with('items');
        if ($requester['requester_type'] == 'user') {
            $query->where('user_id', $requester['user_id']);
        } else {
            $query->where('customer_id', $requester['customer_id']);
        }

        $gaRequest = $query->findOrFail($id);

        $data = array_merge(
            compact('gaRequest'),
            $this->getSharedData(),
            ['current_page' => 'ga.show']
        );

        return view('user.ga.show', $data);
    }

    public function cancel($id)
    {
        $requester = $this->resolveRequester();

        $query = GaRequest::query();
        if ($requester['requester_type'] == 'user') {
            $query->where('user_id', $requester['user_id']);
        } else {
            $query->where('customer_id', $requester['customer_id']);
        }

        $gaRequest = $query->whereIn('status', [GaRequest::STATUS_PENDING_L1, GaRequest::STATUS_PENDING_L2])->findOrFail($id);

        $gaRequest->update([
            'status' => GaRequest::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'l1_token' => null,
            'l2_token' => null,
        ]);

        return redirect()->route('ga.show', $gaRequest->id)
                         ->with('success', 'GA Request dibatalkan.');
    }

    public function downloadPdf($id)
    {
        $requester = $this->resolveRequester();

        $query = GaRequest::query();
        if ($requester['requester_type'] == 'user') {
            $query->where('user_id', $requester['user_id']);
        } else {
            $query->where('customer_id', $requester['customer_id']);
        }

        $gaRequest = $query->findOrFail($id);

        return $this->streamPdf($gaRequest);
    }

    public function downloadPdfSigned($id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Link tidak valid atau sudah kedaluwarsa.');
        }

        $gaRequest = GaRequest::findOrFail($id);

        if ($gaRequest->status != GaRequest::STATUS_APPROVED) {
            abort(403, 'Permintaan belum disetujui.');
        }

        return $this->streamPdf($gaRequest);
    }

    private function streamPdf(GaRequest $gaRequest)
    {
        $pdfService = app(GaPdfService::class);
        $pdf = $pdfService->generate($gaRequest);
        $content = $pdf->output();

        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $pdfService->fileName($gaRequest) . '"')
            ->header('Content-Length', strlen($content));
    }

    public function approveL1($token)
    {
        $gaRequest = GaRequest::where('l1_token', $token)->where('status', GaRequest::STATUS_PENDING_L1)->first();

        if (!$gaRequest) {
            return view('user.ga.approval-result', array_merge(
                ['status' => 'invalid', 'success' => false, 'message' => 'Link persetujuan tidak valid atau sudah digunakan.'],
                $this->getSharedData()
            ));
        }

        $this->processApprove($gaRequest, 'l1');

        return view('user.ga.approval-result', array_merge(
            compact('gaRequest'),
            [
                'status' => GaRequest::STATUS_APPROVED,
                'success' => true,
                'message' => 'GA Request berhasil disetujui.',
            ],
            $this->getSharedData()
        ));
    }

    public function rejectL1($token, Request $request)
    {
        $gaRequest = GaRequest::where('l1_token', $token)->where('status', GaRequest::STATUS_PENDING_L1)->first();

        if (!$gaRequest) {
            return view('user.ga.approval-result', array_merge(
                ['status' => 'invalid', 'success' => false, 'message' => 'Link penolakan tidak valid atau sudah digunakan.'],
                $this->getSharedData()
            ));
        }

        $this->processReject($gaRequest, $request->reason);

        return view('user.ga.approval-result', array_merge(
            compact('gaRequest'),
            [
                'status' => GaRequest::STATUS_REJECTED,
                'success' => false,
                'message' => 'GA Request telah ditolak.',
            ],
            $this->getSharedData()
        ));
    }

    public function approveL2($token)
    {
        $gaRequest = GaRequest::where('l2_token', $token)->where('status', GaRequest::STATUS_PENDING_L2)->first();

        if (!$gaRequest) {
            return view('user.ga.approval-result', array_merge(
                ['status' => 'invalid', 'success' => false, 'message' => 'Link persetujuan tidak valid atau sudah digunakan.'],
                $this->getSharedData()
            ));
        }

        $this->processApprove($gaRequest, 'l2');

        return view('user.ga.approval-result', array_merge(
            compact('gaRequest'),
            [
                'status' => GaRequest::STATUS_APPROVED,
                'success' => true,
                'message' => 'GA Request berhasil disetujui pada level 2.',
            ],
            $this->getSharedData()
        ));
    }

    public function rejectL2($token, Request $request)
    {
        $gaRequest = GaRequest::where('l2_token', $token)->where('status', GaRequest::STATUS_PENDING_L2)->first();

        if (!$gaRequest) {
            return view('user.ga.approval-result', array_merge(
                ['status' => 'invalid', 'success' => false, 'message' => 'Link penolakan tidak valid atau sudah digunakan.'],
                $this->getSharedData()
            ));
        }

        $this->processReject($gaRequest, $request->reason);

        return view('user.ga.approval-result', array_merge(
            compact('gaRequest'),
            [
                'status' => GaRequest::STATUS_REJECTED,
                'success' => false,
                'message' => 'GA Request telah ditolak.',
            ],
            $this->getSharedData()
        ));
    }

    private function processApprove(GaRequest $gaRequest, $layer)
    {
        app(GaWorkflowService::class)->approve($gaRequest, $layer);
    }

    private function processReject(GaRequest $gaRequest, $reason = null)
    {
        app(GaWorkflowService::class)->reject($gaRequest, null, $reason);
    }

    private function fullName($user)
    {
        if (!empty($user->name)) {
            return $user->name;
        }
        if (!empty($user->firstname)) {
            return $user->firstname . ' ' . ($user->lastname ?? '');
        }
        return $user->email;
    }

    private function applicantData($requester)
    {
        if ($requester['requester_type'] == 'user') {
            $user = $requester['user'];
            $columns = Schema::getColumnListing('users');

            return [
                'nama_lengkap' => $this->fullName($user),
                'jabatan' => in_array('jabatan', $columns) ? ($user->jabatan ?? null) : null,
                'departemen' => in_array('departements', $columns) ? ($user->departements ?? null) : null,
                'entitas' => in_array('entitas', $columns) ? ($user->entitas ?? null) : null,
                'email' => $user->email,
                'no_hp' => $user->phone ? (string) $user->phone : null,
            ];
        }

        $customer = $requester['customer'];

        return [
            'nama_lengkap' => trim(($customer->firstname ?? '') . ' ' . ($customer->lastname ?? '')),
            'jabatan' => $customer->jabatan ?? null,
            'departemen' => $customer->departemen ?? null,
            'entitas' => null,
            'email' => $customer->email,
            'no_hp' => $customer->phone ? (string) $customer->phone : null,
        ];
    }
}