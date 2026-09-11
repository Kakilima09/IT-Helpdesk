@extends('layouts.papdmaster')

@section('content')
<section>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $status == 'approved' ? 'Permintaan Disetujui' : 'Permintaan Ditolak' }}</h4>
                    </div>
                    <div class="card-body">
                        @if($status == 'approved')
                            <div class="alert alert-success">
                                <i class="fe fe-check-circle"></i> {{ $message ?? 'Permintaan PAPD berhasil disetujui.' }}
                            </div>
                        @else
                            <div class="alert alert-danger">
                                <i class="fe fe-x-circle"></i> {{ $message ?? 'Permintaan PAPD telah ditolak.' }}
                            </div>
                        @endif

                        <!-- ===== DETAIL PERMOHONAN (selalu ditampilkan) ===== -->
                        <table class="table table-bordered">
                            <tr><th>No SPPD</th><td>{{ $papdRequest->no_sppd ?? '-' }}</td></tr>
                            <tr><th>Pemohon</th><td>{{ $papdRequest->nama_lengkap ?? '-' }}</td></tr>
                            <tr><th>Departemen</th><td>{{ $papdRequest->departemen ?? '-' }}</td></tr>
                            <tr><th>Tujuan</th><td>{{ $papdRequest->kota_tujuan ?? '-' }}</td></tr>
                            <tr>
                                <th>Keberangkatan</th>
                                <td>
                                    @if($papdRequest->tanggal_keberangkatan && $papdRequest->jam_keberangkatan)
                                        {{ \Carbon\Carbon::parse($papdRequest->tanggal_keberangkatan)->setTimeFromTimeString($papdRequest->jam_keberangkatan)->format('d M Y H:i') }}
                                    @else
                                        {{ $papdRequest->tanggal_keberangkatan ? \Carbon\Carbon::parse($papdRequest->tanggal_keberangkatan)->format('d M Y') : '-' }}
                                        {{ $papdRequest->jam_keberangkatan ?? '' }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kepulangan</th>
                                <td>
                                    @if($papdRequest->tanggal_kepulangan && $papdRequest->jam_kepulangan)
                                        {{ \Carbon\Carbon::parse($papdRequest->tanggal_kepulangan)->setTimeFromTimeString($papdRequest->jam_kepulangan)->format('d M Y H:i') }}
                                    @else
                                        {{ $papdRequest->tanggal_kepulangan ? \Carbon\Carbon::parse($papdRequest->tanggal_kepulangan)->format('d M Y') : '-' }}
                                        {{ $papdRequest->jam_kepulangan ?? '' }}
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <!-- ===== TOMBOL AKSI ===== -->
                        <div class="mt-4">
                            @if($status == 'approved')
                                <a href="{{ route('papd.downloadPdf', $papdRequest->id) }}" class="btn btn-success">
                                    <i class="fe fe-download"></i> Download PDF
                                </a>
                                {{-- <a href="{{ route('papd.print', $papdRequest->id) }}" target="_blank" class="btn btn-primary">
                                    <i class="fe fe-printer"></i> Print
                                </a> --}}
                            @endif
                            <a href="{{ route('papd.index') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection