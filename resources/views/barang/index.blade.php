@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary mt-1" href="{{ url('barang/create') }}">Tambah</a>
                <button onclick="modalAction('{{ url('/barang/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah
                    Ajax</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="form-group">
                <label for="kategori_id">Filter Kategori:</label>
                <select id="kategori_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->kategori_id }}">{{ $kat->kategori_nama }}</option>
                    @endforeach
                </select>
            </div>

            <table class="table table-bordered table-striped table-hover table-sm" id="table_barang">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
            data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection
    @push('css')
    @endpush
    @push('js')
        <script>
            function modalAction(url = '') {
                $('#myModal').load(url, function () {
                    $('#myModal').modal('show');
                });
            }

            var dataBarang;
            $(document).ready(function () {
                dataBarang = $('#table_barang').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: "{{ url('barang/list') }}",
                        type: "POST",
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: function (d) {
                            d.kategori_id = $('#kategori_id').val(); // Mengirimkan filter kategori
                        },
                        error: function (xhr, error, thrown) {
                            console.log("AJAX Error:", xhr.responseText);
                        }
                    },
                    columns: [
                        { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                        { data: "barang_kode", orderable: true, searchable: true },
                        { data: "barang_nama", orderable: true, searchable: true },
                        { data: "kategori_nama", orderable: true, searchable: true },
                        { data: "harga_beli", className: "text-right", orderable: true, searchable: true },
                        { data: "harga_jual", className: "text-right", orderable: true, searchable: true },
                        { data: "aksi", orderable: false, searchable: false }
                    ]
                });

                $('#kategori_id').on('change', function () {
                    dataBarang.ajax.reload();
                });
            });
        </script>
    @endpush