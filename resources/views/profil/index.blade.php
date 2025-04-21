@extends('layouts.template')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-user-circle mr-2"></i>Profil Pengguna</h3>
    </div>
    <div class="card-body">
        <div class="row">

            {{-- Kolom kiri: Foto dan Upload --}}
            <div class="col-md-5 border-right text-center">
                {{-- Foto Profil --}}
                <div class="mb-3">
                    @if (auth()->user()->foto_profil)
                        <img src="{{ asset(auth()->user()->foto_profil) }}" alt="Foto Profil"
                             class="img-circle elevation-3 mb-2"
                             style="width: 180px; height: 180px; object-fit: cover;">
                    @else
                        <img src="{{ asset('default-avatar.png') }}" alt="Default Foto"
                             class="img-circle elevation-3 mb-2"
                             style="width: 180px; height: 180px; object-fit: cover;">
                    @endif
                </div>

                {{-- Form Upload --}}
                <form action="{{ route('profil.uploadFoto', ['id' => auth()->user()->user_id]) }}"
                      method="POST" enctype="multipart/form-data" class="px-3">
                    @csrf
                    <div class="form-group">
                        <input type="file" name="foto_profil" class="form-control" onchange="previewFoto(event)">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-upload"></i> Upload Foto
                    </button>
                </form>

                {{-- Preview Sebelum Upload --}}
                <div class="mt-3">
                    <img id="preview" src="#" alt="Preview"
                         class="img-circle elevation-1 border border-secondary"
                         style="display:none; width:150px; height:150px; object-fit:cover;">
                </div>
            </div>

            {{-- Kolom kanan: Info Pengguna --}}
            <div class="col-md-7">
                <h5 class="mb-3"><i class="fas fa-info-circle mr-1"></i> Informasi Akun</h5>
                <table class="table table-striped table-sm">
                    <tr>
                        <th><i class="fas fa-user mr-1 text-primary"></i> Nama</th>
                        <td>{{ auth()->user()->nama }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-user-tag mr-1 text-primary"></i> Username</th>
                        <td>{{ auth()->user()->username ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-user-shield mr-1 text-primary"></i> Level</th>
                        <td>{{ ucfirst(auth()->user()->level->level_nama) }}</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewFoto(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('preview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush

@endsection
