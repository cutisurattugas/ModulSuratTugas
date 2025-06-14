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
        document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi umum untuk semua .tomselect
    document.querySelectorAll('.tomselect').forEach(function (element) {
        // Hindari inisialisasi ulang elemen yang sudah dihandle secara khusus
        if (
            element.id !== 'alat_angkutan_individu' &&
            element.id !== 'alat_angkutan_tim'
        ) {
            new TomSelect(element, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }
    });

    // Inisialisasi khusus untuk alat angkutan
    const angkutanOptions = {
        create: true,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: "-- Pilih atau ketik angkutan --"
    };

    if (document.getElementById("alat_angkutan_individu")) {
        new TomSelect("#alat_angkutan_individu", angkutanOptions);
    }
    if (document.getElementById("alat_angkutan_tim")) {
        new TomSelect("#alat_angkutan_tim", angkutanOptions);
    }
});

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi Toggle Alat Angkutan & Kota Keberangkatan/Tujuan
            const forms = [{
                    jarakId: 'jarak_individu',
                    alatContainerId: 'alat_angkutan_container_individu',
                    alatSelectId: 'alat_angkutan_individu',
                    kotaFieldsId: 'kota_fields_individu'
                },
                {
                    jarakId: 'jarak_tim',
                    alatContainerId: 'alat_angkutan_container_tim',
                    alatSelectId: 'alat_angkutan_tim',
                    kotaFieldsId: 'kota_fields_kelompok'
                }
            ];

            forms.forEach(({
                jarakId,
                alatContainerId,
                alatSelectId,
                kotaFieldsId
            }) => {
                const jarakSelect = document.getElementById(jarakId);
                const alatAngkutanContainer = document.getElementById(alatContainerId);
                const alatAngkutanSelect = document.getElementById(alatSelectId);
                const kotaFields = document.getElementById(kotaFieldsId);

                if (jarakSelect && alatAngkutanContainer && kotaFields) {
                    function updateFields() {
                        const isLuarKota = jarakSelect.value === 'luar_kota';

                        // Tampilkan/menghilangkan field sesuai kondisi
                        alatAngkutanContainer.style.display = isLuarKota ? 'block' : 'none';
                        kotaFields.style.display = isLuarKota ? 'flex' : 'none';

                        // Reset value jika tidak dalam kondisi luar_kota
                        if (!isLuarKota) {
                            if (alatAngkutanSelect) alatAngkutanSelect.value = '';
                        }
                    }

                    // Jalankan saat halaman pertama kali dimuat
                    updateFields();

                    // Event listener
                    jarakSelect.addEventListener('change', updateFields);
                }
            });
        });
    </script>

@endsection
