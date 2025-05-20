@extends('adminlte::page')
@section('title', 'Tambah Dinas Luar')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endsection

@section('content_header')
    <h1 class="m-0 text-dark">Tambah Pengajuan Dinas Luar</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="dinasLuarTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="individu-tab" data-bs-toggle="tab"
                                data-bs-target="#individu" type="button" role="tab" aria-controls="individu"
                                aria-selected="true">
                                Surat Tugas Individu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kelompok-tab" data-bs-toggle="tab" data-bs-target="#kelompok"
                                type="button" role="tab" aria-controls="kelompok" aria-selected="false">
                                Surat Tugas Kelompok
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="dinasLuarTabContent">
                        <div class="tab-pane fade show active" id="individu" role="tabpanel"
                            aria-labelledby="individu-tab">
                            @include('surattugas::surattugas.components.individu')
                        </div>
                        <div class="tab-pane fade" id="kelompok" role="tabpanel" aria-labelledby="kelompok-tab">
                            @include('surattugas::surattugas.components.kelompok')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('adminlte_js')
    {{-- Bootstrap Bundle (required for tabs to work properly) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".flatpickr", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });
    </script>

    {{-- Tom Select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tomselect').forEach(function(element) {
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide alat angkutan based on jarak selection
            const jarakSelect = document.querySelector('select[name="jarak"]');
            const alatAngkutanContainer = document.getElementById('alat_angkutan_container');

            jarakSelect.addEventListener('change', function() {
                alatAngkutanContainer.style.display = this.value === 'luar_kota' ? 'block' : 'none';
                if (this.value !== 'luar_kota') {
                    document.getElementById('alat_angkutan').value = '';
                }
            });

            // Trigger change event on page load if needed
            jarakSelect.dispatchEvent(new Event('change'));
        });
    </script>
@endsection
