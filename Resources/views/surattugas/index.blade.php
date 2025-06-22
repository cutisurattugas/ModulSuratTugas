@extends('adminlte::page')
@section('title', 'Surat Tugas')

@section('content_header')
    <h1 class="m-0 text-dark">Surat Tugas Perjalanan Dinas</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="lead">
                        Kelola surat tugas perjalanan dinas.
                        @if (auth()->user()->role_aktif === 'admin')
                            <a href="{{ route('surattugas.create') }}" class="btn btn-primary btn-sm float-right">Buat
                                Surat</a>
                        @endif
                        @if (in_array(auth()->user()->role_aktif, ['dosen', 'pegawai', 'wadir1', 'wadir2', 'wadir3', 'kaunit']))
                            <a href="#" class="btn btn-primary btn-sm float-right">Unduh Format Laporan</a>
                        @endif
                    </div>

                    <div class="mt-2">
                        @include('layouts.partials.messages')
                    </div>

                    {{-- Navigasi Tab Dinamis --}}
                    <ul class="nav nav-tabs mb-3" id="dinasLuarTab" role="tablist">

                        {{-- Tab Individu & Kelompok hanya untuk bukan direktur --}}
                        @if (!in_array(auth()->user()->role_aktif, ['direktur']))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="individu-tab" data-bs-toggle="tab"
                                    data-bs-target="#individu" type="button" role="tab">
                                    Individu
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="kelompok-tab" data-bs-toggle="tab" data-bs-target="#kelompok"
                                    type="button" role="tab">
                                    Tim
                                </button>
                            </li>
                        @endif

                        {{-- Tab Semua Surat Tugas hanya untuk direktur, wadir1, wadir2, wadir3 --}}
                        @if (in_array(auth()->user()->role_aktif, ['direktur', 'wadir1', 'wadir2', 'wadir3']))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ auth()->user()->role_aktif === 'direktur' ? 'active' : '' }}"
                                    id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button"
                                    role="tab">
                                    Semua Surat Tugas
                                </button>
                            </li>
                        @endif

                    </ul>

                    {{-- Konten Tab Dinamis --}}
                    <div class="tab-content" id="dinasLuarTabContent">

                        {{-- Tab Individu --}}
                        @if (!in_array(auth()->user()->role_aktif, ['direktur']))
                            <div class="tab-pane fade show active" id="individu" role="tabpanel">
                                @include('surattugas::surattugas.components.tabel', [
                                    'mode' => 'individu',
                                    'dinas_data' => $dinas_individu,
                                ])
                            </div>
                        @endif

                        {{-- Tab Kelompok --}}
                        @if (!in_array(auth()->user()->role_aktif, ['direktur']))
                            <div class="tab-pane fade" id="kelompok" role="tabpanel">
                                @include('surattugas::surattugas.components.tabel', [
                                    'mode' => 'kelompok',
                                    'dinas_data' => $dinas_tim,
                                ])
                            </div>
                        @endif

                        {{-- Tab Semua Surat Tugas --}}
                        @if (in_array(auth()->user()->role_aktif, ['direktur', 'wadir1', 'wadir2', 'wadir3']))
                            <div class="tab-pane fade show {{ auth()->user()->role_aktif === 'direktur' ? 'active' : '' }}"
                                id="semua" role="tabpanel">
                                @include('surattugas::surattugas.components.tabel', [
                                    'mode' => 'semua',
                                    'dinas_data' => $dinas_semua,
                                ])
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
