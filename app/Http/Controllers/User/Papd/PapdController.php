<?php

namespace App\Http\Controllers\User\Papd;

use App\Http\Controllers\Controller;
use App\Models\Papd\PapdRequest;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use App\Models\Customfield;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Papd\AtasanApprovalNotification;
use App\Mail\Papd\ApprovedUserNotification;
use App\Mail\Papd\ApprovedCorporateNotification;
use App\Mail\Papd\RejectedUserNotification;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PapdNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PapdController extends Controller
{
    /**
     * Ambil semua data global yang dibutuhkan oleh layout dan menu
     */
    private function getSharedData()
    {
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $seopage = Seosetting::first();

        // Pastikan $page selalu berupa Collection (untuk menu navigasi)
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

        // ========== TAMBAHKAN ANNOUNCEMENT ==========
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

        return compact(
            'title',
            'footertext',
            'seopage',
            'page',
            'customfields',
            'announcement',  // <-- tambahkan
            'announcements'  // <-- tambahkan
        );
    }

    /**
     * Dashboard PAPD (index)
     */
    public function index()
    {
        $user = auth()->user();
        $papdRequests = PapdRequest::where('user_id', $user->id)->get();

        $total = $papdRequests->count();
        $pending = $papdRequests->where('status', 'pending')->count();
        $approved = $papdRequests->where('status', 'approved')->count();
        $rejected = $papdRequests->where('status', 'rejected')->count();
        $expired = $papdRequests->where('status', 'expired')->count();

        $data = array_merge(
            compact('papdRequests', 'total', 'pending', 'approved', 'rejected', 'expired'),
            $this->getSharedData(),
            ['current_page' => 'papd'] // <-- key baru untuk menu aktif
        );

        return view('user.papd.index', $data);
    }

    /**
     * Menampilkan form PAPD (create)
     */
    public function create()
    {
        $user = auth()->user();
        $data = array_merge(
            compact('user'),
            $this->getSharedData(),
            ['current_page' => 'papd.create']
        );
        return view('user.papd.create', $data);
    }

    /**
     * Menyimpan permintaan PAPD
     */
    public function store(Request $request)
    {
        // Aturan validasi dasar
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'id_karyawan' => 'required|string|max:50',
            'nik_ktp' => 'required|string|max:20',
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'entitas' => 'nullable|string|max:255',
            'atasan_nama' => 'required|string|max:255',
            'atasan_email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'ttl' => 'required|date',
            'no_paspor' => 'nullable|string|max:50',
            'exp_date_paspor' => 'nullable|date|after:today',
            'jenis_perjalanan' => 'required|in:domestik,internasional',
            'nama_paspor' => 'nullable|string|max:255',
            'kota_tujuan' => 'required|string|max:255',
            'agenda' => 'required|string',
            'no_sppd' => 'nullable|string|max:100',
            'tanggal_keberangkatan' => 'required|date|after_or_equal:today',
            'jam_keberangkatan' => 'required',
            'tanggal_kepulangan' => 'nullable|date|after:tanggal_keberangkatan',
            'jam_kepulangan' => 'nullable',
            'durasi_hari' => 'nullable|integer|min:0',
            'pembebanan_biaya' => 'required|string|max:255',
            'moda_transportasi' => 'required|in:pesawat,kereta,bus,kapal,whoosh,lainnya',
            'kelas' => 'nullable|string|max:50',
            'rute' => 'nullable|string|max:255',
            'detail_maskapai' => 'nullable|string|max:255',
            'no_penerbangan' => 'nullable|string|max:50',
            'bagasi_tambahan' => 'boolean',
            'transportasi_lokal' => 'boolean',
            'opsi_transportasi' => 'nullable|array',
            'hotel_reservasi' => 'boolean',
            'nama_hotel' => 'nullable|string|max:255',
            'lokasi_hotel' => 'nullable|string|max:255',
            'alamat_hotel' => 'nullable|string',
            'check_in' => 'nullable|date|after_or_equal:tanggal_keberangkatan',
            'check_out' => 'nullable|date|after:check_in',
            'jumlah_kamar' => 'nullable|integer|min:1',
            'permintaan_khusus' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        // Jika jenis perjalanan internasional, nama_paspor wajib diisi
        if ($request->jenis_perjalanan == 'internasional') {
            $rules['nama_paspor'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Jika durasi_hari tidak diisi dan kedua tanggal ada, hitung otomatis
        if (empty($validated['durasi_hari']) && $request->filled('tanggal_keberangkatan') && $request->filled('tanggal_kepulangan')) {
            $start = \Carbon\Carbon::parse($request->tanggal_keberangkatan);
            $end   = \Carbon\Carbon::parse($request->tanggal_kepulangan);
            if ($end >= $start) {
                $validated['durasi_hari'] = $end->diffInDays($start) + 1; // inklusif
            }
        }

        // Jika durasi masih kosong, set default 0 atau 1 sesuai kebijakan
        if (empty($validated['durasi_hari'])) {
            $validated['durasi_hari'] = 0; // atau 1 jika ingin minimal 1
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        $papdRequest = PapdRequest::create($validated);

        try {
            $user = auth()->user();
            $user->notify(new PapdNotification(
                $papdRequest->id,
                'Permintaan PAPD Dikirim',
                'Permintaan PAPD Anda telah dikirim dan menunggu persetujuan atasan.',
                'pending',
                route('papd.show', $papdRequest->id)
            ));
        } catch (\Exception $e) {
            Log::error('Gagal kirim notifikasi database ke pemohon: ' . $e->getMessage());
        }

        Mail::to($papdRequest->atasan_email)->send(
            new AtasanApprovalNotification($papdRequest)
        );

        return redirect()->route('papd.index')
                         ->with('success', 'Permintaan PAPD berhasil dikirim, silakan tunggu persetujuan atasan.');
    }

    /**
     * Proses Approve via token
     */
    public function approve($token)
    {
        try {
            $papdRequest = PapdRequest::where('approval_token', $token)
                                    ->where('status', 'pending')
                                    ->firstOrFail();

            $papdRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approval_token' => null,
            ]);

            try {
                // Kirim notifikasi database ke corporate/admin
                $corporateUsers = User::whereIn('role', ['corporate', 'admin'])->get();
                if ($corporateUsers->count() > 0) {
                    foreach ($corporateUsers as $corpUser) {
                        $corpUser->notify(new PapdNotification(
                            $papdRequest->id,
                            'Permintaan PAPD Disetujui',
                            'Permintaan PAPD dari ' . $papdRequest->nama_lengkap . ' (ID: ' . $papdRequest->id . ') telah disetujui oleh atasan.',
                            'approved',
                            route('admin.papd.index')
                        ));
                    }
                    \Log::info('Notifikasi PAPD terkirim ke ' . $corporateUsers->count() . ' user corporate/admin');
                }
            } catch (\Exception $e) {
                \Log::error('Gagal kirim notifikasi database ke corporate: ' . $e->getMessage());
            }

            //Notifikasi ke user
            try {
                $user = User::find($papdRequest->user_id); // atau pakai $papdRequest->user
                if ($user) {
                    $user->notify(new PapdNotification(
                        $papdRequest->id,
                        'Permintaan PAPD Disetujui',
                        'Permintaan PAPD Anda telah disetujui oleh atasan.',
                        'approved',
                        route('papd.show', $papdRequest->id)
                    ));
                }
            } catch (\Exception $e) {
                Log::error('Gagal kirim notifikasi database approve: ' . $e->getMessage());
            }

            // ======== KIRIM EMAIL KE PEMOHON ========
            try {
                $recipient = $papdRequest->email;
                \Log::info('Mencoba kirim email approve ke pemohon: ' . $recipient);

                Mail::to($recipient)->send(new ApprovedUserNotification($papdRequest));

                \Log::info('Email approve ke pemohon berhasil dikirim: ' . $recipient);
            } catch (\Exception $e) {
                \Log::error('Gagal kirim email approve ke pemohon: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                // Kirim fallback plain text
                try {
                    Mail::raw("Permintaan PAPD Anda telah disetujui.\nDetail: " . route('papd.show', $papdRequest->id), function ($message) use ($papdRequest) {
                        $message->to($papdRequest->email)
                                ->subject('Permintaan Perjalanan Dinas Disetujui');
                    });
                    \Log::info('Fallback email ke pemohon terkirim');
                } catch (\Exception $e2) {
                    \Log::error('Fallback email ke pemohon gagal: ' . $e2->getMessage());
                }
            }

            // ======== KIRIM EMAIL KE CORPORATE ========
            $corporateEmail = config('app.corporate_email', 'corporatesecretary@indovisual.co.id');
            if (!empty($corporateEmail)) {
                try {
                    \Log::info('Mencoba kirim email ke corporate: ' . $corporateEmail);

                    Mail::to($corporateEmail)->send(new ApprovedCorporateNotification($papdRequest));

                    \Log::info('Email ke corporate berhasil dikirim: ' . $corporateEmail);
                } catch (\Exception $e) {
                    \Log::error('Gagal kirim email ke corporate: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    // Fallback plain text
                    try {
                        Mail::raw("Permintaan PAPD telah disetujui.\nData lengkap: " . route('papd.show', $papdRequest->id), function ($message) use ($corporateEmail) {
                            $message->to($corporateEmail)
                                    ->subject('Data PAPD untuk Pemesanan');
                        });
                        \Log::info('Fallback email ke corporate terkirim');
                    } catch (\Exception $e2) {
                        \Log::error('Fallback email ke corporate gagal: ' . $e2->getMessage());
                    }
                }
            } else {
                \Log::warning('Email corporate tidak diatur, skip.');
            }

            // Data untuk view
            $data = array_merge(
                compact('papdRequest'),
                [
                    'status' => 'approved',
                    'success' => true,
                    'message' => 'Permintaan PAPD berhasil disetujui.',
                ],
                $this->getSharedData() ?? [],
                ['current_page' => 'papd.approval']
            );
            return view('user.papd.approval-result', $data);

        } catch (\Exception $e) {
            \Log::error('Error saat approve PAPD: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses persetujuan.');
        }
    }

    public function downloadPdf($id)
    {
        try {
            $papdRequest = PapdRequest::where('user_id', auth()->id())->findOrFail($id);
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
            \Log::error('Download PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mendownload PDF.');
        }
    }

    /**
     * Proses Reject via token
     */
    public function reject($token)
    {
        try {
            $papdRequest = PapdRequest::where('approval_token', $token)
                                    ->where('status', 'pending')
                                    ->firstOrFail();

            $papdRequest->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'approval_token' => null,
            ]);

            // Notifikasi database ke pemohon
            try {
                $user = User::find($papdRequest->user_id);
                if ($user) {
                    $user->notify(new PapdNotification(
                        $papdRequest->id,
                        'Permintaan PAPD Ditolak',
                        'Permintaan PAPD Anda telah ditolak oleh atasan.',
                        'rejected',
                        route('papd.show', $papdRequest->id)
                    ));
                }
            } catch (\Exception $e) {
                Log::error('Gagal kirim notifikasi database reject: ' . $e->getMessage());
            }

            // Kirim email ke user pemohon (langsung pakai Mailable)
            try {
                Mail::to($papdRequest->email)->send(
                    new RejectedUserNotification($papdRequest)
                );
                \Log::info('Email reject terkirim ke pemohon: ' . $papdRequest->email);
            } catch (\Exception $e) {
                \Log::error('Gagal kirim email reject ke user: ' . $e->getMessage());
            }

            // Data untuk view
            $data = array_merge(
                compact('papdRequest'),
                [
                    'status' => 'rejected',
                    'success' => false,
                    'message' => 'Permintaan PAPD telah ditolak.',
                ],
                $this->getSharedData() ?? [],
                ['current_page' => 'papd.reject']
            );

            return view('user.papd.approval-result', $data);

        } catch (\Exception $e) {
            \Log::error('Error saat reject PAPD: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses penolakan.');
        }
    }

    /**
     * Menampilkan detail permintaan PAPD
     */
    public function show($id)
    {
        $papdRequest = PapdRequest::where('user_id', auth()->id())
                                  ->findOrFail($id);

        $data = array_merge(
            compact('papdRequest'),
            $this->getSharedData(),
            ['current_page' => 'papd.show']
        );

        return view('user.papd.show', $data);
    }

    /**
     * Menampilkan form edit (hanya jika status pending)
     */
    public function edit($id)
    {
        $papdRequest = PapdRequest::where('user_id', auth()->id())
                                  ->where('status', 'pending')
                                  ->findOrFail($id);

        $data = array_merge(
            compact('papdRequest'),
            $this->getSharedData(),
            ['current_page' => 'papd.edit']
        );

        return view('user.papd.edit', $data);
    }

    /**
     * Memperbarui permintaan PAPD (hanya jika status pending)
     */
    public function update(Request $request, $id)
    {
        $papdRequest = PapdRequest::where('user_id', auth()->id())
                                ->where('status', 'pending')
                                ->findOrFail($id);

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'id_karyawan' => 'required|string|max:50',
            'nik_ktp' => 'required|string|max:20',
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'entitas' => 'nullable|string|max:255',
            'atasan_nama' => 'required|string|max:255',
            'atasan_email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'ttl' => 'required|date',
            'no_paspor' => 'nullable|string|max:50',
            'exp_date_paspor' => 'nullable|date|after:today',
            'jenis_perjalanan' => 'required|in:domestik,internasional',
            'nama_paspor' => 'required|string|max:255',
            'kota_tujuan' => 'required|string|max:255',
            'agenda' => 'required|string',
            'no_sppd' => 'nullable|string|max:100',
            'tanggal_keberangkatan' => 'required|date|after_or_equal:today',
            'jam_keberangkatan' => 'required',
            'tanggal_kepulangan' => 'nullable|date|after:tanggal_keberangkatan',
            'jam_kepulangan' => 'nullable',
            'durasi_hari' => 'nullable|integer|min:0',
            'pembebanan_biaya' => 'required|string|max:255',
            'moda_transportasi' => 'required|in:pesawat,kereta,bus,kapal,whoosh,lainnya',
            'kelas' => 'nullable|string|max:50',
            'rute' => 'nullable|string|max:255',
            'detail_maskapai' => 'nullable|string|max:255',
            'no_penerbangan' => 'nullable|string|max:50',
            'bagasi_tambahan' => 'boolean',
            'transportasi_lokal' => 'boolean',
            'opsi_transportasi' => 'nullable|array',
            'hotel_reservasi' => 'boolean',
            'nama_hotel' => 'nullable|string|max:255',
            'lokasi_hotel' => 'nullable|string|max:255',
            'alamat_hotel' => 'nullable|string',
            'check_in' => 'nullable|date|after_or_equal:tanggal_keberangkatan',
            'check_out' => 'nullable|date|after:check_in',
            'jumlah_kamar' => 'nullable|integer|min:1',
            'permintaan_khusus' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        if ($request->jenis_perjalanan == 'internasional') {
            $rules['nama_paspor'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Hitung ulang durasi jika tanggal diisi
        if (empty($validated['durasi_hari']) && $request->filled('tanggal_keberangkatan') && $request->filled('tanggal_kepulangan')) {
            $start = \Carbon\Carbon::parse($request->tanggal_keberangkatan);
            $end   = \Carbon\Carbon::parse($request->tanggal_kepulangan);
            if ($end >= $start) {
                $validated['durasi_hari'] = $end->diffInDays($start) + 1;
            }
        }

        if (empty($validated['durasi_hari'])) {
            $validated['durasi_hari'] = 0;
        }

        $papdRequest->update($validated);

        return redirect()->route('papd.show', $papdRequest->id)
                        ->with('success', 'Permintaan PAPD berhasil diperbarui.');
    }

    /**
     * Hapus permintaan PAPD (hanya jika status pending)
     */
    public function destroy($id)
    {
        $papdRequest = PapdRequest::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->findOrFail($id);
        $papdRequest->delete();

        return redirect()->route('papd.index')
                         ->with('success', 'Permintaan PAPD berhasil dihapus.');
    }

    /**
     * Hapus massal permintaan PAPD (hanya yang status pending)
     */
    public function massDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $deleted = PapdRequest::where('user_id', auth()->id())
                              ->whereIn('id', $ids)
                              ->where('status', 'pending')
                              ->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Data PAPD berhasil dihapus massal.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data. Pastikan data berstatus pending.');
        }
    }

    private function generatePdf(PapdRequest $papdRequest)
    {
        // Ambil logo dari file 
        $logoPath = public_path('assets/images/logo.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        } else {
            // Fallback: jika logo tidak ada, bisa gunakan teks atau placeholder
            $logoBase64 = null;
        }

        $pdf = Pdf::loadView('user.pdf.papd-approval', [
            'papdRequest' => $papdRequest,
            'logo' => $logoBase64
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }

    /**
     * Ambil data user berdasarkan nama (untuk autocomplete)
     */
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
            $hasEmployee = in_array('empid', $columns);
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
                // Tentukan nama lengkap
                $fullName = $user->name;
                if (empty($fullName) && $hasFirstname && $hasLastname) {
                    $fullName = $user->firstname . ' ' . $user->lastname;
                } elseif (empty($fullName) && $hasFirstname) {
                    $fullName = $user->firstname;
                }
                if (empty($fullName)) {
                    $fullName = $user->email; // fallback
                }

                $results[] = [
                    'id'          => $fullName,
                    'text'        => $fullName . ' (' . $user->email . ')',
                    'name'        => $fullName,
                    'email'       => $user->email,
                    'employee_id' => $hasEmployee ? ($user->empid ?? '') : '',
                    'department'  => $hasDepartment ? ($user->departments ?? '') : '',
                ];
            }

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Error di getUserData: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function printView($id)
    {
        // Ambil logo dari file 
        $logoPath = public_path('assets/images/logo.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        } else {
            // Fallback: jika logo tidak ada, bisa gunakan teks atau placeholder
            $logoBase64 = null;
        }

        $pdf = Pdf::loadView('user.papd.print', [
            'logo' => $logoBase64
        ]);

         // Ambil data permintaan milik user yang sedang login
        $papdRequest = PapdRequest::where('user_id', auth()->id())->findOrFail($id);

        // Kirim ke view print
        return view('user.papd.print', compact('papdRequest'));
    }

    /**
     * Sanitasi nama file agar aman untuk file system
     */
    private function sanitizeFileName($name)
    {
        // Hapus karakter yang tidak diizinkan, ganti dengan underscore
        return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    }

    public function downloadPdfSigned($id, Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                abort(401, 'Link tidak valid atau sudah kedaluwarsa.');
            }

            $papdRequest = PapdRequest::findOrFail($id);

            if ($papdRequest->status != 'approved') {
                abort(403, 'Permintaan belum disetujui.');
            }

            $pdf = $this->generatePdf($papdRequest);
            $pdfContent = $pdf->output();

            $cleanName = $this->sanitizeFileName($papdRequest->nama_lengkap);
            $fileName = 'PAPD-' . $cleanName . '.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->header('Content-Length', strlen($pdfContent));
        } catch (\Exception $e) {
            \Log::error('Download PDF signed error: ' . $e->getMessage());
            abort(500, 'Gagal mendownload PDF.');
        }
    }

    /**
     * Menampilkan halaman notifikasi khusus PAPD
     */
    public function notifications(Request $request)
    {
        $user = auth()->user();

        $query = $user->notifications()
            ->where('data->papd_id', '!=', null);

        // Filter berdasarkan kata kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('data->mailsubject', 'LIKE', "%{$search}%")
                ->orWhere('data->mailtext', 'LIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan status PAPD (pending, approved, etc)
        if ($request->filled('status')) {
            $query->where('data->papd_status', $request->status);
        }

        // Filter berdasarkan read/unread
        if ($request->filled('read_status')) {
            if ($request->read_status == 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->read_status == 'read') {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);

        // Ambil data PAPD
        $notifications->each(function ($notification) {
            $papdId = $notification->data['papd_id'] ?? null;
            if ($papdId) {
                $notification->papd = \App\Models\Papd\PapdRequest::find($papdId);
            }
        });

        $data = array_merge(
            compact('notifications'),
            $this->getSharedData(),
            ['current_page' => 'papd.notifications']
        );

        return view('user.papd.notification', $data);
    }

    public function markAllRead()
    {
        $user = Auth::guard('customer')->user();
        if ($user) {
            // Hanya tandai notifikasi yang memiliki data 'papd_id' sebagai sudah dibaca
            $user->unreadNotifications()
                ->where('data->papd_id', '!=', null)
                ->update(['read_at' => now()]);
            
            return response()->json(['success' => true, 'message' => 'Semua notifikasi PAPD telah ditandai sebagai sudah dibaca.']);
        }
        
        return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
    }

    // * Menampilkan form edit profile untuk user PAPD
    // */
    public function editProfile()
    {
        $user = auth()->user();
        $data = array_merge(
            compact('user'),
            $this->getSharedData(),
            ['current_page' => 'papd.profile']
        );
        return view('user.papd.profile.edit', $data);
    }

    /**
     * Update profile user PAPD
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname'  => 'nullable|string|max:255',
            'username'  => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        // Jika password diisi, validasi
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['current_password'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Cek current password jika password diisi
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        // Upload foto
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/profile');
            
            // Hapus foto lama jika ada
            if ($user->image && file_exists($destinationPath . '/' . $user->image)) {
                unlink($destinationPath . '/' . $user->image);
            }
            
            $file->move($destinationPath, $filename);
            $validated['image'] = $filename;
        } else {
            unset($validated['image']);
        }

        // Update user
        $user->update($validated);

        return redirect()->route('papd.profile.edit')
                        ->with('success', 'Profile berhasil diperbarui.');
    }
}