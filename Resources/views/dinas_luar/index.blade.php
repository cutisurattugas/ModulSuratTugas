@extends('adminlte::page')
@section('title', 'Dinas Luar')

@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1>Dinas Luar</h1>
                    <div class="lead">
                        Manajemen pengajuan dinas luar.
                        {{-- Tombol buat pengajuan nanti bisa dikondisikan berdasarkan role --}}
                        <a href="{{route('dinas_luar.create')}}" class="btn btn-primary btn-sm float-right">Buat Surat</a>
                    </div>

                    <div class="mt-2">
                        @include('layouts.partials.messages')
                    </div>

                    {{-- Tab Header --}}
                    <ul class="nav nav-tabs mb-3" id="dinasLuarTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="individu-tab" data-bs-toggle="tab"
                                data-bs-target="#individu" type="button" role="tab">Individu</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kelompok-tab" data-bs-toggle="tab"
                                data-bs-target="#kelompok" type="button" role="tab">Kelompok</button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="dinasLuarTabContent">
                        <div class="tab-pane fade show active" id="individu" role="tabpanel">
                            @include('surattugas::dinas_luar.components.tabel', ['mode' => 'individu', 'dinas_data' => []])
                        </div>
                        <div class="tab-pane fade" id="kelompok" role="tabpanel">
                            @include('surattugas::dinas_luar.components.tabel', ['mode' => 'kelompok', 'dinas_data' => []])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
