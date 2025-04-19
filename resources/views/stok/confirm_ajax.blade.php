<div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <form action="{{ url('/stok/' . $stok->stok_id . '/delete_ajax') }}" method="POST" id="form-delete">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus stok dari barang <strong>{{ $stok->barang->barang_nama }}</strong>?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    $('#form-delete').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: this.action,
            method: this.method,
            data: $(this).serialize(),
            success: function (res) {
                $('#myModal').modal('hide');
                if (res.status) {
                    Swal.fire('Berhasil', res.message, 'success');
                    tableStok.ajax.reload();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    });
</script>