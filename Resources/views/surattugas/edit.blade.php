@extends('adminlte::page')
@section('title', 'Edit Surat Tugas')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endsection

@section('content_header')
    <h1 class="m-0 text-dark">Edit Surat Tugas</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('surattugas.update', $suratTugas->access_token) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="jenis" value="{{ $suratTugas->jenis }}">

                        <!-- Show only the relevant tab based on jenis -->
                        @if ($suratTugas->jenis === 'individu')
                            @include('surattugas::surattugas.components.individu-edit')
                        @elseif ($suratTugas->jenis === 'tim')
                            @include('surattugas::surattugas.components.kelompok-edit')
                        @endif

                        <div class="form-group row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Perbarui</button>
                                <a href="{{ route('surattugas.index') }}" class="btn btn-default">Kembali</a>
                            </div>
                        </div>
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
            // Initialize TomSelect for all select elements
            document.querySelectorAll('.tomselect-edit').forEach(function(element) {
                new TomSelect(element, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });

            // Show/hide alat angkutan based on jarak selection
            const jarakSelect = document.querySelector('select[name="jarak"]');
            if (jarakSelect) {
                const alatAngkutanContainer = document.getElementById('alat_angkutan_container');
                
                jarakSelect.addEventListener('change', function() {
                    if (alatAngkutanContainer) {
                        alatAngkutanContainer.style.display = this.value === 'luar_kota' ? 'block' : 'none';
                        if (this.value !== 'luar_kota') {
                            const alatAngkutan = document.getElementById('alat_angkutan');
                            if (alatAngkutan) alatAngkutan.value = '';
                        }
                    }
                });

                // Trigger initial state
                jarakSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection