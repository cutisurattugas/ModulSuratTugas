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
            // Inisialisasi untuk select umum
            document.querySelectorAll('.tomselect-edit').forEach(function(element) {
                new TomSelect(element, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });

            // Inisialisasi alat angkutan individu
            if (document.querySelector("#alat_angkutan_individu")) {
                new TomSelect("#alat_angkutan_individu", {
                    create: true,
                    persist: true,
                    placeholder: "-- Pilih atau ketik angkutan --",
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            }

            // Inisialisasi alat angkutan tim
            if (document.querySelector("#alat_angkutan_tim")) {
                new TomSelect("#alat_angkutan_tim", {
                    create: true,
                    persist: true,
                    placeholder: "-- Pilih atau ketik angkutan --",
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            }

            // Show/hide alat angkutan based on jarak selection
            const forms = [{
                jarakId: 'jarak_individu',
                alatContainerId: 'alat_angkutan_container_individu',
                kotaFieldsId: 'kota_fields_individu'
            }, {
                jarakId: 'jarak_kelompok',
                alatContainerId: 'alat_angkutan_container_tim',
                kotaFieldsId: 'kota_fields_kelompok'
            }];

            forms.forEach(({
                jarakId,
                alatContainerId,
                kotaFieldsId
            }) => {
                const jarakSelect = document.getElementById(jarakId);
                const alatAngkutanContainer = document.getElementById(alatContainerId);
                const kotaFields = document.getElementById(kotaFieldsId);

                if (jarakSelect && alatAngkutanContainer && kotaFields) {
                    function updateFields() {
                        const isLuarKota = jarakSelect.value === 'luar_kota';

                        // Tampilkan/menghilangkan field sesuai kondisi
                        alatAngkutanContainer.style.display = isLuarKota ? 'block' : 'none';
                        kotaFields.style.display = isLuarKota ? 'flex' : 'none';

                        // Reset value jika dalam_kota
                        if (!isLuarKota) {
                            const alatAngkutan = alatAngkutanContainer.querySelector('select');
                            const kotaKeberangkatan = kotaFields.querySelector(
                                'input[name="kota_keberangkatan"]');
                            const kotaTujuan = kotaFields.querySelector('input[name="kota_tujuan"]');

                            if (alatAngkutan) alatAngkutan.value = '';
                            if (kotaKeberangkatan) kotaKeberangkatan.value = '';
                            if (kotaTujuan) kotaTujuan.value = '';
                        }
                    }

                    // Jalankan sekali saat halaman dimuat
                    updateFields();

                    // Event listener
                    jarakSelect.addEventListener('change', updateFields);
                }
            });
        });
    </script>
@endsection
