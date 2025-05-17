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
                                Dinas Luar Individu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kelompok-tab" data-bs-toggle="tab" data-bs-target="#kelompok"
                                type="button" role="tab" aria-controls="kelompok" aria-selected="false">
                                Dinas Luar Kelompok
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="dinasLuarTabContent">
                        <div class="tab-pane fade show active" id="individu" role="tabpanel"
                            aria-labelledby="individu-tab">
                            @include('surattugas::dinas_luar.components.individu')
                        </div>
                        <div class="tab-pane fade" id="kelompok" role="tabpanel" aria-labelledby="kelompok-tab">
                            @include('surattugas::dinas_luar.components.kelompok')
                        </div>
                    </div>
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
@endsection
