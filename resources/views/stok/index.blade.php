@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Stok Barang</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Stok Barang</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i>
                    Export Stok Barang</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok Barang</a>
                <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-success">Tambah Stok
                    (Ajax)</button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Data -->
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_barang" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_barang" class="form-control form-control-sm filter_barang">
                                    <option value="">- Semua -</option>
                                    @foreach($barang as $b)
                                        <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Nama Barang</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-sm table-striped table-hover" id="stok_id">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Tanggal Stok</th>
                        <th>Jumlah</th>
                        <th>Input Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false"
        data-width="75%"></div>
@endsection

@push('js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var tableStok;
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            tableStok = $('#stok_id').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('stok/list') }}",
                    type: "POST",
                    data: function (d) {
                        d.filter_barang = $('.filter_barang').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        width: "5%",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "barang.barang_nama",
                        width: "25%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "stok_tanggal",
                        width: "20%",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "stok_jumlah",
                        width: "15%",
                        className: "text-right",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "user.nama", // sesuaikan jika field nama user berbeda
                        width: "20%",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        width: "15%",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#stok_id_filter input').unbind().bind('keyup', function (e) {
                if (e.keyCode == 13) {
                    tableStok.search(this.value).draw();
                }
            });

            $('.filter_barang').change(function () {
                tableStok.draw();
            });
        });
    </script>
@endpush