@extends('layouts.template')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Halo, apa kabar!!!</h3>
        <div class="card-tools"></div>
    </div>
    <div class="card-body">

        {{-- Tampilkan foto profil dari database --}}
        @if (auth()->user()->foto_profil)
            <div class="text-center mb-3">
                <img src="{{ asset(auth()->user()->foto_profil) }}" alt="Foto Profil"
                    class="rounded-circle border border-primary" width="150" height="150" style="object-fit: cover;">
            </div>
        @endif

        {{-- Form upload foto --}}
        <form action="{{ route('profil.uploadFoto', ['id' => auth()->user()->user_id]) }}" method="POST"
              enctype="multipart/form-data">
            @csrf
            <input type="file" name="foto_profil" class="form-control" onchange="previewFoto(event)" />
            <button type="submit" class="btn btn-primary mt-2">Upload</button>
        </form>

        {{-- Preview foto sebelum upload --}}
        <div class="text-center">
            <img id="preview" src="#" alt="Preview"
                 class="mt-3 rounded-circle border border-secondary"
                 style="display:none; width:150px; height:150px; object-fit:cover;">
        </div>

    </div>
</div>

@push('scripts')
    <script>
        function previewFoto(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endpush

@endsection
