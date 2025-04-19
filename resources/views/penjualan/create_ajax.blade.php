<form action="{{ url('/penjualan/store_ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" required>
                    <small id="error-pembeli" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Tanggal Penjualan</label>
                    <input type="date" name="penjualan_tanggal" class="form-control" required>
                    <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                </div>

                <hr>
                <h6>Barang yang Dijual</h6>
                <div id="list-barang">
                    <div class="form-row mb-2 barang-item">
                        <div class="col-5">
                            <select name="barang_id[]" class="form-control" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($barang as $b)
                                    <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" required>
                        </div>
                        <div class="col-3">
                            <input type="number" name="harga[]" class="form-control" placeholder="Harga Per Satuan" required>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger btn-remove">Hapus</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="btn-tambah-barang" class="btn btn-success btn-sm mt-2">+ Tambah Barang</button>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function () {
        $('#btn-tambah-barang').on('click', function () {
            const item = $('.barang-item').first().clone();
            item.find('input, select').val('');
            $('#list-barang').append(item);
        });

        $(document).on('click', '.btn-remove', function () {
            if ($('.barang-item').length > 1) {
                $(this).closest('.barang-item').remove();
            }
        });

        $("#form-tambah").validate({
            rules: {
                pembeli: { required: true },
                penjualan_tanggal: { required: true }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: "POST",
                    data: $(form).serialize(),
                    success: function (response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire('Berhasil!', response.message, 'success');
                            tablePenjualan.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function (key, val) {
                                $('#error-' + key).text(val[0]);
                            });
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
