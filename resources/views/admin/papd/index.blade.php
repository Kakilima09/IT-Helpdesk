@extends('layouts.adminmasterpapd')

@section('styles')
<!-- Data table css -->
<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/datatable/buttonbootstrap.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/sweet-alert/sweetalert.css')}}" rel="stylesheet" />
@endsection

@section('content')
<!-- Page header -->
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="font-weight-normal text-muted ms-2">{{ lang('PAPD Management') }}</span></h4>
    </div>
</div>
<!-- End Page header -->

@php
    $user = auth()->user();
    $isSuperadmin = $user->hasRole('superadmin') || $user->hasRole('Superadmin') || strtolower($user->role) == 'superadmin';
    $isCorporate = $user->hasRole('corporate') || $user->hasRole('Corporate') || strtolower($user->role) == 'corporate';
    $isAdmin = !$isSuperadmin && !$isCorporate; // admin biasa
    $userDepartment = $user->departemen ?? null;
@endphp

<!-- Statistik Cards -->
<div class="row">
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg class="ticket-new primary svg-primary" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                            <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3 c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1 c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859 C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859 C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                            <rect width="2" height="2" x="41.963" y="27"></rect>
                            <rect width="2" height="2" x="41.963" y="31"></rect>
                            <rect width="2" height="2" x="41.963" y="19"></rect>
                            <rect width="2" height="2" x="41.963" y="35"></rect>
                            <rect width="2" height="2" x="41.963" y="23"></rect>
                            <rect width="2" height="2" x="41.963" y="39"></rect>
                        </svg>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Total Approved')}}</p>
                        <h5 class="mb-0">{{ $totalApproved }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg class="ticket-new warning svg-warning" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                            <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3 c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1 c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859 C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859 C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                            <rect width="2" height="2" x="41.963" y="27"></rect>
                            <rect width="2" height="2" x="41.963" y="31"></rect>
                            <rect width="2" height="2" x="41.963" y="19"></rect>
                            <rect width="2" height="2" x="41.963" y="35"></rect>
                            <rect width="2" height="2" x="41.963" y="23"></rect>
                            <rect width="2" height="2" x="41.963" y="39"></rect>
                        </svg>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Pending')}}</p>
                        <h5 class="mb-0">{{ $totalPending }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg class="ticket-new danger svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                            <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3 c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1 c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859 C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859 C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                            <rect width="2" height="2" x="41.963" y="27"></rect>
                            <rect width="2" height="2" x="41.963" y="31"></rect>
                            <rect width="2" height="2" x="41.963" y="19"></rect>
                            <rect width="2" height="2" x="41.963" y="35"></rect>
                            <rect width="2" height="2" x="41.963" y="23"></rect>
                            <rect width="2" height="2" x="41.963" y="39"></rect>
                        </svg>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Rejected')}}</p>
                        <h5 class="mb-0">{{ $totalRejected }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg class="ticket-new secondary svg-secondary" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                            <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3 c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1 c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859 C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859 C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                            <rect width="2" height="2" x="41.963" y="27"></rect>
                            <rect width="2" height="2" x="41.963" y="31"></rect>
                            <rect width="2" height="2" x="41.963" y="19"></rect>
                            <rect width="2" height="2" x="41.963" y="35"></rect>
                            <rect width="2" height="2" x="41.963" y="23"></rect>
                            <rect width="2" height="2" x="41.963" y="39"></rect>
                        </svg>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Expired')}}</p>
                        <h5 class="mb-0">{{ $totalExpired }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg class="ticket-new success svg-success" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                            <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3 c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1 c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859 C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859 C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                            <rect width="2" height="2" x="41.963" y="27"></rect>
                            <rect width="2" height="2" x="41.963" y="31"></rect>
                            <rect width="2" height="2" x="41.963" y="19"></rect>
                            <rect width="2" height="2" x="41.963" y="35"></rect>
                            <rect width="2" height="2" x="41.963" y="23"></rect>
                            <rect width="2" height="2" x="41.963" y="39"></rect>
                        </svg>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Done')}}</p>
                        <h5 class="mb-0">{{ $totalDone ?? 0 }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg class="ticket-new danger svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                            <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3 c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1 c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859 C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859 C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                            <rect width="2" height="2" x="41.963" y="27"></rect>
                            <rect width="2" height="2" x="41.963" y="31"></rect>
                            <rect width="2" height="2" x="41.963" y="19"></rect>
                            <rect width="2" height="2" x="41.963" y="35"></rect>
                            <rect width="2" height="2" x="41.963" y="23"></rect>
                            <rect width="2" height="2" x="41.963" y="39"></rect>
                        </svg>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Cancel')}}</p>
                        <h5 class="mb-0">{{ $totalCancel ?? 0 }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter dan Export -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{lang('Filter Data')}}</h4>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.papd.index') }}" class="row g-3 align-items-end">
            <!-- Filter Departemen (hanya untuk Superadmin & Corporate) -->
            @if($isSuperadmin || $isCorporate)
                <div class="col-md-2">
                    <label class="form-label">Departemen</label>
                    <select name="department" class="form-select">
                        <option value="">Semua</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- Admin biasa hanya melihat data departemennya sendiri -->
                <div class="col-md-2">
                    <label class="form-label">Departemen</label>
                    <input type="text" class="form-control" value="{{ $userDepartment }}" disabled>
                    <input type="hidden" name="department" value="{{ $userDepartment }}">
                </div>
            @endif

            <div class="col-md-2">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Closing Status</label>
                <select name="closing_status" class="form-select">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('closing_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="done" {{ request('closing_status') == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="cancel" {{ request('closing_status') == 'cancel' ? 'selected' : '' }}>Cancel</option>
                </select>
            </div>
            <!-- Filter Pembebanan Biaya -->
            <div class="col-md-2">
                <label class="form-label">Pembebanan Biaya</label>
                <select name="pembebanan_biaya" class="form-select">
                    <option value="">Semua</option>
                    @foreach($pembebananOptions as $option)
                        <option value="{{ $option }}" {{ request('pembebanan_biaya') == $option ? 'selected' : '' }}>{{ $option ?: '-' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.papd.export', request()->query()) }}" class="btn btn-success w-100">
                    <i class="fe fe-download"></i> Export Excel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            {{lang('Daftar Semua Permintaan PAPD')}}
            @if($isAdmin && $userDepartment)
                <span class="badge badge-info ms-2">Departemen: {{ $userDepartment }}</span>
            @endif
        </h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered w-100" id="papdAdminTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pemohon</th>
                        <th>Pembebanan Biaya</th>
                        <th>Tgl Pengajuan</th>
                        <th>Transportasi / Akomodasi</th>
                        <th>Keberangkatan</th>
                        <th>Status Corporate</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($papdRequests as $papd)
                    <tr>
                        <td>{{ $papd->id }}</td>
                        <td>{{ $papd->nama_lengkap }}</td>
                        <td>{{ $papd->pembebanan_biaya ?? '-' }}</td>
                        <td>{{ $papd->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            <strong>{{ ucfirst($papd->moda_transportasi) }}</strong>
                            @if($papd->hotel_reservasi && $papd->nama_hotel)
                                <br><span class="text-muted">Hotel: {{ $papd->nama_hotel }}</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($papd->tanggal_keberangkatan)->format('d-m-Y') }} {{ $papd->jam_keberangkatan }}</td>
                        <td>
                            @if($papd->closing_status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($papd->closing_status == 'done')
                                <span class="badge badge-success">Done</span>
                            @elseif($papd->closing_status == 'cancel')
                                <span class="badge badge-danger">Cancel</span>
                            @else
                                <span class="badge badge-secondary">-</span>
                            @endif
                            <br>
                            <small class="text-muted">{{ strtoupper($papd->status) }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.papd.show', $papd->id) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fe fe-eye"></i>
                                </a>
                                <a href="{{ route('admin.papd.downloadPdf', $papd->id) }}" class="btn btn-success btn-sm" title="Download PDF">
                                    <i class="fe fe-download"></i>
                                </a>
                            
                                @if($isCorporate || $isSuperadmin)
                                    @if($papd->status == 'approved' && $papd->closing_status == 'pending')
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#closeModal{{ $papd->id }}" title="Update Closing">
                                            <i class="fe fe-check-circle"></i>
                                        </button>
                                    @elseif($papd->status == 'approved' && in_array($papd->closing_status, ['done', 'cancel']))
                                        <!-- Ubah inline-block menjadi inline agar form tidak memecah fleksibelitas flexbox -->
                                        <form method="POST" action="{{ route('admin.papd.reopen', $papd->id) }}" style="display:inline; margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="Re-Open (Kembalikan ke Pending)" onclick="return confirm('Yakin ingin membuka kembali PAPD ini?');">
                                                <i class="fe fe-refresh-cw"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    <!-- Modal Update Closing -->
                    <div class="modal fade" id="closeModal{{ $papd->id }}" tabindex="-1" aria-labelledby="closeModalLabel{{ $papd->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.papd.close', $papd->id) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="closeModalLabel{{ $papd->id }}">Update Closing - PAPD #{{ $papd->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="closing_status" class="form-select" required>
                                                <option value="done">Done (Selesai)</option>
                                                <option value="cancel">Cancel (Batal)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Catatan (Opsional)</label>
                                            <textarea name="closing_note" class="form-control" rows="3" placeholder="Masukkan catatan closing..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-center">Belum ada data PAPD.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-end mt-3">
            {{ $papdRequests->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // 1. Inisialisasi DataTable
        $('#papdAdminTable').DataTable({
            responsive: true,
            ordering: true,
            paging: false,
            info: false,
            order: [[0, 'desc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                emptyTable: "Tidak ada data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                zeroRecords: "Tidak ditemukan data yang sesuai",
            }
        });

        // 2. Nonaktifkan tombol modal yang targetnya tidak ditemukan (cegah error)
        // $('[data-bs-toggle="modal"]').each(function() {
        //     var target = $(this).data('bs-target');
        //     if (target && !$(target).length) {
        //         $(this).removeAttr('data-bs-toggle');
        //         $(this).removeAttr('data-bs-target');
        //         console.warn('Modal target not found, disabled:', target);
        //     }
        // });

        // 3. Fallback manual untuk membuka modal (jika data-bs-toggle gagal)
        $(document).on('click', '[data-bs-target^="#closeModal"]', function(e) {
            // Cek apakah tombol masih memiliki data-bs-toggle (jika sudah dinonaktifkan, lewati)
            if ($(this).data('bs-toggle') === 'modal') {
                var target = $(this).data('bs-target');
                if (target && $(target).length) {
                    try {
                        var modal = new bootstrap.Modal(target);
                        modal.show();
                    } catch (error) {
                        console.warn('Fallback modal gagal:', error);
                    }
                }
            }
        });
    });
</script>
@endsection