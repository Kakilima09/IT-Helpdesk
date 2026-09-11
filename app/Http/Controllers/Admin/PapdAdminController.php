<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Papd\PapdRequest;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PapdExport;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PapdAdminController extends Controller
{
    private function getSharedData()
    {
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $seopage = Seosetting::first();
        $page = Pages::all();
        return compact('title', 'footertext', 'seopage', 'page');
    }

    public function index(Request $request)
    {
        $data = $this->getSharedData();
        $user = auth()->user();

        // ===== ROLE DETECTION =====
        $isSuperadmin = $user->hasRole('superadmin') || $user->hasRole('Superadmin') || strtolower($user->role ?? '') == 'superadmin';
        $isCorporate = $user->hasRole('corporate') || $user->hasRole('Corporate') || strtolower($user->role ?? '') == 'corporate';
        $isAdmin = !$isSuperadmin && !$isCorporate;

        $userDepartment = $user->departments ?? null; // sesuai kolom di users

        // ===== QUERY UTAMA =====
        $query = PapdRequest::query();

        // Filter otomatis untuk admin biasa (dibatasi departemen)
        if ($isAdmin) {
            if ($userDepartment) {
                $query->where('departemen', $userDepartment);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Filter manual
        if ($request->filled('department') && ($isSuperadmin || $isCorporate)) {
            $query->where('departemen', $request->department);
        }

        // ===== TAMBAHKAN FILTER PEMBEBANAN BIAYA =====
        if ($request->filled('pembebanan_biaya')) {
            $query->where('pembebanan_biaya', $request->pembebanan_biaya);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('closing_status')) {
            $query->where('closing_status', $request->closing_status);
        }

        $papdRequests = $query->orderBy('created_at', 'desc')->paginate(20);

        // ===== STATISTIK =====
        $statQuery = PapdRequest::query();

        if ($isAdmin) {
            if ($userDepartment) {
                $statQuery->where('departemen', $userDepartment);
            } else {
                $statQuery->whereRaw('1 = 0');
            }
        }

        if ($request->filled('department') && ($isSuperadmin || $isCorporate)) {
            $statQuery->where('departemen', $request->department);
        }

        // Tambahkan filter pembebanan biaya ke statistik
        if ($request->filled('pembebanan_biaya')) {
            $statQuery->where('pembebanan_biaya', $request->pembebanan_biaya);
        }

        $totalApproved = (clone $statQuery)->where('status', 'approved')->count();

        if ($isSuperadmin || $isCorporate) {
            $totalPending = (clone $statQuery)->where('status', 'approved')->where('closing_status', 'pending')->count();
        } else {
            $totalPending = (clone $statQuery)->where('status', 'pending')->count();
        }

        $totalRejected = (clone $statQuery)->where('status', 'rejected')->count();
        $totalExpired  = (clone $statQuery)->where('status', 'expired')->count();
        $totalDone     = (clone $statQuery)->where('closing_status', 'done')->count();
        $totalCancel   = (clone $statQuery)->where('closing_status', 'cancel')->count();

        // ===== DROPDOWN DATA =====
        if ($isSuperadmin || $isCorporate) {
            $departments = PapdRequest::distinct()->pluck('departemen');
        } else {
            $departments = collect([$userDepartment])->filter();
        }

        // Ambil daftar pembebanan biaya unik untuk dropdown filter
        $pembebananOptions = PapdRequest::distinct()->pluck('pembebanan_biaya')->filter();

        $data['papdRequests']     = $papdRequests;
        $data['totalApproved']    = $totalApproved;
        $data['totalPending']     = $totalPending;
        $data['totalRejected']    = $totalRejected;
        $data['totalExpired']     = $totalExpired;
        $data['totalDone']        = $totalDone;
        $data['totalCancel']      = $totalCancel;
        $data['departments']      = $departments;
        $data['pembebananOptions']= $pembebananOptions;
        $data['current_page']     = 'papd-admin';

        return view('admin.papd.index', $data);
    }

    public function export(Request $request)
    {
        $filters = [
            'department' => $request->department,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ];
        return Excel::download(new PapdExport($filters), 'papd-approved-report.xlsx');
    }

    public function updateClosing(Request $request, $id)
    {
        $user = auth()->user();

        // ===== ROLE DETECTION (sama dengan index) =====
        $isCorporate = $user->hasRole('corporate') || $user->hasRole('Corporate') || strtolower($user->role ?? '') == 'corporate';
        $isSuperadmin = $user->hasRole('superadmin') || $user->hasRole('Superadmin') || strtolower($user->role ?? '') == 'superadmin';

        if (!$isCorporate && !$isSuperadmin) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan closing.');
        }

        $papd = PapdRequest::findOrFail($id);

        if ($papd->status != 'approved') {
            return redirect()->back()->with('error', 'Hanya PAPD yang sudah approved yang dapat di-close.');
        }

        if ($papd->closing_status != 'pending') {
            return redirect()->back()->with('error', 'PAPD ini sudah memiliki status closing.');
        }

        $validated = $request->validate([
            'closing_status' => 'required|in:done,cancel',
            'closing_note'   => 'nullable|string|max:500',
        ]);

        $papd->update([
            'closing_status' => $validated['closing_status'],
            'closed_at'      => now(),
            'closing_note'   => $validated['closing_note'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Status closing berhasil diperbarui.');
    }

    public function markAllRead()
    {
        $user = auth()->user();
        if ($user) {
            $user->unreadNotifications()->where('data->papd_id', '!=', null)->update(['read_at' => now()]);
            return response()->json(['success' => true, 'message' => 'Semua notifikasi PAPD telah ditandai sudah dibaca.']);
        }
        return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
    }

    /**
     * Menampilkan detail permintaan PAPD untuk admin
     */
    public function show($id)
    {
        $papdRequest = PapdRequest::findOrFail($id);
        
        $data = $this->getSharedData();
        $data['papdRequest'] = $papdRequest;
        $data['current_page'] = 'papd-admin';
        
        return view('admin.papd.show', $data);
    }

    /**
     * Download PDF untuk admin (tanpa filter user_id)
     */
    public function downloadPdf($id)
    {
        try {
            $papdRequest = PapdRequest::findOrFail($id);
            
            // Generate PDF
            $pdf = $this->generatePdf($papdRequest);
            $pdfContent = $pdf->output();

            // Sanitasi nama pemohon
            $cleanName = $this->sanitizeFileName($papdRequest->nama_lengkap);
            $fileName = 'PAPD-' . $cleanName . '.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->header('Content-Length', strlen($pdfContent));
        } catch (\Exception $e) {
            \Log::error('Admin Download PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mendownload PDF.');
        }
    }

    /**
     * Generate PDF (sama seperti di user controller)
     */
    private function generatePdf(PapdRequest $papdRequest)
    {
        $logoPath = public_path('assets/images/logo.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $pdf = Pdf::loadView('user.pdf.papd-approval', [
            'papdRequest' => $papdRequest,
            'logo' => $logoBase64
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }

    /**
     * Sanitasi nama file
     */
    private function sanitizeFileName($name)
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    }

    /**
     * Menampilkan halaman profile admin dengan layout PAPD
     */
    public function profile()
    {
        $user = auth()->user();
        $data = $this->getSharedData();
        $data['user'] = $user;
        $data['current_page'] = 'papd-admin-profile';
        
        return view('admin.papd.profile', $data);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        $user = auth()->user();

        // Hapus foto lama jika ada
        if ($user->image && $user->image != 'user-profile.png') {
            $oldPath = public_path('uploads/profile/' . $user->image);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        // Upload foto baru
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $user->image = $filename;
            $user->save();

            return redirect()->route('admin.papd.profile')->with('success', 'Foto profil berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal mengupload foto.');
    }

    /**
     * Re-open PAPD yang sudah di-close (kembalikan ke pending)
     */
    public function reopen($id)
    {
        $user = auth()->user();

        // Hanya corporate atau superadmin yang boleh
        $isCorporate = $user->hasRole('corporate') || $user->hasRole('Corporate') || strtolower($user->role ?? '') == 'corporate';
        $isSuperadmin = $user->hasRole('superadmin') || $user->hasRole('Superadmin') || strtolower($user->role ?? '') == 'superadmin';

        if (!$isCorporate && !$isSuperadmin) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan re-open.');
        }

        $papd = PapdRequest::findOrFail($id);

        // Hanya bisa re-open jika status sudah di-close (done/cancel)
        if (!in_array($papd->closing_status, ['done', 'cancel'])) {
            return redirect()->back()->with('error', 'PAPD ini belum di-close, tidak perlu re-open.');
        }

        // Kembalikan ke pending
        $papd->update([
            'closing_status' => 'pending',
            'closed_at'      => null,
            'closing_note'   => null,
        ]);

        return redirect()->back()->with('success', 'PAPD berhasil di-reopen (kembali ke status pending).');
    }
}