@extends('adminlte::page')
@section('title', 'Tambah Surat Tugas')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endsection

@section('content_header')
    <h1 class="m-0 text-dark">Tambah Pengajuan Surat Tugas</h1>
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
                                Surat Tugas Tim
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
            // Mapping form IDs
            const forms = [{
                    jarakId: 'jarak_individu',
                    containerId: 'alat_angkutan_container_individu',
                    selectId: 'alat_angkutan_individu'
                },
                {
                    jarakId: 'jarak_tim',
                    containerId: 'alat_angkutan_container_tim',
                    selectId: 'alat_angkutan_tim'
                }
            ];

            forms.forEach(({
                jarakId,
                containerId,
                selectId
            }) => {
                const jarakSelect = document.getElementById(jarakId);
                const alatAngkutanContainer = document.getElementById(containerId);
                const alatAngkutanSelect = document.getElementById(selectId);

                if (jarakSelect && alatAngkutanContainer && alatAngkutanSelect) {
                    jarakSelect.addEventListener('change', function() {
                        if (this.value === 'luar_kota') {
                            alatAngkutanContainer.style.display = 'block';
                        } else {
                            alatAngkutanContainer.style.display = 'none';
                            alatAngkutanSelect.value = '';
                        }
                    });

                    // Trigger once on page load
                    jarakSelect.dispatchEvent(new Event('change'));
                }
            });
        });
    </script>

@endsection
