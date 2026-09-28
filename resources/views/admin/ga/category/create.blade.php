@extends('layouts.adminmasterga')

@section('styles')
@endsection

@section('content')
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="font-weight-normal text-muted ms-2">{{ lang('Tambah Kategori Barang') }}</span></h4>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.ga.categories.store') }}" class="row">
            @csrf
            <div class="col-md-6 mb-3">
                <label class="form-label">Kode <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" maxlength="20" placeholder="Contoh: ATK / RTK" value="{{ old('code') }}" required>
                <small class="text-muted">Disimpan sebagai huruf kapital.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" maxlength="255" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-12 mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Aktif</label>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.ga.categories.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@endsection