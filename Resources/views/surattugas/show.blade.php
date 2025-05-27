@extends('adminlte::page')

@section('title', 'Detail Surat Tugas')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Surat Tugas</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p><strong>Nomor Surat:</strong> {{ $surat->nomor_surat }}</p>
                    <p><strong>Jenis:</strong> {{ ucfirst($surat->jenis) }}</p>
                    <p><strong>Jarak:</strong> {{ str_replace('_', ' ', ucfirst($surat->jarak)) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($surat->status) }}</p>
                    <p><strong>Disetujui oleh Wadir 2:</strong>
                        {{ $surat->tanggal_disetujui_wadir2 ? $surat->tanggal_disetujui_wadir2->format('d M Y') : '-' }}
                    </p>
                    <p><strong>Disetujui oleh Direktur:</strong>
                        {{ $surat->tanggal_disetujui_pimpinan ? $surat->tanggal_disetujui_pimpinan->format('d M Y') : '-' }}
                    </p>
                    <div class="row mt-2 align-items-center">
                        <div class="col-auto">
                            @if (auth()->user()->role_aktif === 'wadir2' && !$surat->tanggal_disetujui_wadir2)
                                <form method="POST" action="{{ route('surattugas.approve', $surat->access_token) }}">
                                    @csrf
                                    <button class="btn btn-primary">Setujui Sebagai Wadir 2</button>
                                </form>
                            @elseif(auth()->user()->role_aktif === 'direktur')
                                @if ($surat->tanggal_disetujui_wadir2 && !$surat->tanggal_disetujui_pimpinan)
                                    <form method="POST" action="{{ route('surattugas.approve', $surat->access_token) }}">
                                        @csrf
                                        <button class="btn btn-primary">Setujui Sebagai Direktur</button>
                                    </form>
                                @else
                                    <div class="alert alert-warning mt-3 mb-0">
                                        Menunggu persetujuan dari Wadir 2 sebelum Anda dapat menyetujui.
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="col-auto">
                            <a href="{{ route('surattugas.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
