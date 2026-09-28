@extends('layouts.adminmasterga')

@section('styles')
<!-- Data table css -->
<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
@endsection

@section('content')
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="font-weight-normal text-muted ms-2">{{ lang('Kategori Barang GA') }}</span></h4>
    </div>
    <div class="page-rightheader ms-auto">
        <a href="{{ route('admin.ga.categories.create') }}" class="btn btn-primary">
            <i class="fe fe-plus"></i> {{ lang('Tambah Kategori') }}
        </a>
        <a href="{{ route('admin.ga.index') }}" class="btn btn-light">
            <i class="fe fe-arrow-left"></i> {{ lang('Kembali') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{lang('Daftar Kategori Barang')}}</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered w-100" id="categoryTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td><span class="badge bg-primary">{{ $category->code }}</span></td>
                        <td>{{ $category->name }}</td>
                        <td>
                            @if($category->status)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.ga.categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fe fe-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.ga.categories.destroy', $category->id) }}" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');" style="display:inline; margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fe fe-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#categoryTable').DataTable({
            ordering: true,
            paging: false,
            info: false,
            order: [[0, 'asc']],
            language: {
                search: "Cari:",
                emptyTable: "Tidak ada data",
                zeroRecords: "Tidak ditemukan data yang sesuai",
            }
        });
    });
</script>
@endsection