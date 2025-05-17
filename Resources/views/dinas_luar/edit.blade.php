@extends('adminlte::page')
@section('title', 'Edit Dinas Luar')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endsection

@section('content_header')
    <h1 class="m-0 text-dark">Edit Pengajuan Dinas Luar</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('perjadin.update', $perjalanan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="jenis" value="{{ $perjalanan->jenis }}">

                        <!-- Hanya muat satu tab sesuai jenis -->
                        @if ($perjalanan->jenis === 'individu')
                            @include('surattugas::dinas_luar.components.individu-edit')
                        @elseif ($perjalanan->jenis === 'tim')
                            @include('surattugas::dinas_luar.components.kelompok-edit')
                        @endif

                        <button type="submit" class="btn btn-primary mt-3">Perbarui</button>
                        <a href="{{ route('perjadin.index') }}" class="btn btn-default mt-3">Kembali</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('adminlte_js')
{{-- bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- flat pickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".flatpickr", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });
    </script>

    {{-- tom select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tomselect-edit').forEach(function(element) {
                new TomSelect(element, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });
        });
    </script>
@endsection