@extends('layouts.usermasterga')

@section('styles')
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/select2-accessible/css/select2-bootstrap4.min.css')}}" rel="stylesheet" />
<style>
    .item-row td { vertical-align: middle; }
</style>
@endsection

@section('content')
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0">{{lang('Buat GA Request')}}</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('ga.index')}}" class="text-white-50">{{lang('Home')}}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">{{lang('Buat GA Request')}}</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="cover-image sptb">
        <div class="container">
            <div class="row">
                @include('includes.user.verticalmenuga')

                <div class="col-xl-9">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
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
                        <div class="card-header">
                            <h3 class="card-title">{{lang('Form GA Request')}}</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('ga.store') }}" id="gaForm">
                                @csrf

                                @php
                                    $applicantName = $requester['requester_type'] == 'user'
                                        ? (trim($requester['user']->name ?? '') ?: trim(($requester['user']->firstname ?? '') . ' ' . ($requester['user']->lastname ?? '')))
                                        : trim(($requester['customer']->firstname ?? '') . ' ' . ($requester['customer']->lastname ?? ''));
                                    $applicantEmail = $requester['requester_type'] == 'user' ? $requester['user']->email : $requester['customer']->email;
                                @endphp

                                <div class="alert alert-info br-3">
                                    <i class="fe fe-user-check me-1"></i>
                                    Data pemohon diambil otomatis dari akun Anda: <strong>{{ $applicantName }}</strong> ({{ $applicantEmail }})
                                </div>

                                <h5 class="mb-3 text-primary"><i class="fe fe-settings me-2"></i>Detail Permintaan</h5>
                                <div class="row">
                                    @if($requester['requester_type'] == 'user')
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Atasan / Approver L1 <span class="text-red">*</span></label>
                                            <select name="atasan_user_id" id="atasan_user_id" class="form-control @error('atasan_user_id') is-invalid @enderror">
                                                <option value="">-- Cari Atasan --</option>
                                            </select>
                                            @error('atasan_user_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Catatan / Keterangan</label>
                                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="mb-3 text-primary"><i class="fe fe-list me-2"></i>Daftar Item</h5>
                                <div class="alert alert-warning-light br-3">
                                    <i class="fe fe-info me-1"></i>
                                    Jika ada harga satu jenis barang melebihi <strong>Rp {{ number_format(config('ga.threshold', 500000), 0, ',', '.') }}</strong>,
                                    request memerlukan persetujuan <strong>Level 2 (GA Manager)</strong>.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="items-table">
                                        <thead>
                                            <tr>
                                                <th width="6%">No</th>
                                                <th width="12%">Jenis</th>
                                                <th width="18%">Kategori</th>
                                                <th>Nama Item</th>
                                                <th width="8%">Satuan</th>
                                                <th width="14%">Harga Satuan</th>
                                                <th width="12%">Subtotal</th>
                                                <th width="6%"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" class="text-end"><strong>Sub Total</strong></td>
                                                <td colspan="2"><input type="text" class="form-control text-end" id="grandTotalDisplay" value="0" readonly></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="text-start mb-4">
                                    <button type="button" class="btn btn-sm btn-secondary" id="addGoodsRow">
                                        <i class="fe fe-plus"></i> Tambah Barang
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info" id="addServiceRow">
                                        <i class="fe fe-plus"></i> Tambah Jasa
                                    </button>
                                </div>

                                <div class="form-group mb-0 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fe fe-send"></i> {{lang('Kirim Request')}}
                                    </button>
                                    <a href="{{ route('ga.index') }}" class="btn btn-secondary btn-lg">{{lang('Batal')}}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#atasan_user_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Ketik nama atau email atasan (min 2 karakter)',
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('ga.getUserData') }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });

        const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values());

        let rowIndex = 0;

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function updateRow(row) {
            const qty = parseFloat(row.find('.item-qty').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            const subtotal = qty * price;
            row.find('.item-subtotal').val(formatRupiah(subtotal));
            row.find('.item-subtotal-raw').val(subtotal);
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            $('.item-subtotal-raw').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('#grandTotalDisplay').val(formatRupiah(total));
        }

        function addRow(type) {
            rowIndex++;
            const isGoods = type === 'goods';
            let categoryCell = '<td></td>';
            if (isGoods) {
                let options = '<option value="">-- Kategori --</option>';
                categories.forEach(function (c) {
                    options += '<option value="' + c.id + '">' + c.name + '</option>';
                });
                categoryCell = '<td><select name="items[' + rowIndex + '][category_id]" class="form-control form-select item-category">' + options + '</select></td>';
            } else {
                categoryCell = '<td><input type="hidden" name="items[' + rowIndex + '][category_id]" value=""></td>';
            }

            const row = $('<tr class="item-row">');
            row.append(
                '<td class="item-no">' + rowIndex + '</td>' +
                '<td>' +
                    '<select name="items[' + rowIndex + '][item_type]" class="form-control form-select item-type">' +
                        '<option value="goods"' + (isGoods ? ' selected' : '') + '>Barang</option>' +
                        '<option value="service"' + (!isGoods ? ' selected' : '') + '>Jasa</option>' +
                    '</select>' +
                '</td>' +
                categoryCell +
                '<td><input type="text" name="items[' + rowIndex + '][name]" class="form-control item-name" placeholder="Nama item" required></td>' +
                '<td><input type="number" name="items[' + rowIndex + '][qty]" class="form-control item-qty" value="1" min="1" placeholder="Jumlah" required></td>' +
                '<td><input type="number" step="0.01" name="items[' + rowIndex + '][price]" class="form-control item-price" value="0" min="0" required></td>' +
                '<td class="text-end"><input type="text" class="form-control item-subtotal text-end" readonly>' + '<input type="hidden" class="item-subtotal-raw" name="items[' + rowIndex + '][amount]"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fe fe-trash"></i></button></td>'
            );
            $('#items-table tbody').append(row);
            updateRow(row);
        }

        $(document).on('input change', '.item-qty, .item-price', function () {
            updateRow($(this).closest('tr'));
        });

        $('#addGoodsRow').on('click', function () { addRow('goods'); });
        $('#addServiceRow').on('click', function () { addRow('service'); });

        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            updateTotal();
        });

        addRow('goods');

        $('#gaForm').on('submit', function (e) {
            if ($('#items-table tbody tr').length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal satu item.');
            }
        });
    });
</script>
@endsection