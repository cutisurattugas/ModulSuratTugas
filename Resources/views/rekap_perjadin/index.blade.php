@extends('adminlte::page')
@section('title', 'Rekap Surat Tugas')

@section('content_header')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1>Rekap Perjalanan Dinas</h1>
                    <form method="GET" action="{{ route('rekap.index') }}" class="form-inline mb-3">

                        <!-- Tahun -->
                        <label for="tahun" class="mr-2">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-control mr-2">
                            @foreach ($daftarTahun as $thn)
                                <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>
                                    {{ $thn }}</option>
                            @endforeach
                        </select>

                        <!-- Nama Pegawai -->
                        <input type="text" name="nama" class="form-control mr-2"
                            placeholder="Cari nama pegawai..." value="{{ request('nama') }}">

                        <!-- Rentang Tanggal -->
                        <label class="mr-2">Dari:</label>
                        <input type="date" name="tanggal_awal" class="form-control mr-2"
                            value="{{ request('tanggal_awal') }}">
                        <label class="mr-2">Sampai:</label>
                        <input type="date" name="tanggal_akhir" class="form-control mr-2"
                            value="{{ request('tanggal_akhir') }}">

                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>

                    <!-- Tombol Print -->
                    <button type="button" class="btn btn-success mb-3" data-toggle="modal"
                        data-target="#printModal">
                        <i class="fas fa-print"></i> Export
                    </button>

                    <table class="table table-bordered">
                        <tr>
                            <th width="1%">No</th>
                            <th>Nama Pegawai</th>
                            <th>Jenis</th>
                            <th>Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Kota Tujuan</th>
                            <th>Alat Angkutan</th>
                            <th>Nomor Surat</th>
                        </tr>
                        @forelse ($suratTugasList as $surat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional(optional($surat->detail)->pegawai)->nama ?? '-' }}
                                </td>
                                <td>{{ ucfirst($surat->jenis) }}</td>
                                <td>{{ ucfirst($surat->detail->kegiatan_maksud) }}</td>
                                <td>
                                    {{ date('d M Y', strtotime($surat->detail->tanggal_mulai)) }} -
                                    {{ date('d M Y', strtotime($surat->detail->tanggal_selesai)) }}
                                </td>
                                <td>{{ optional($surat->detail)->kota_tujuan ?? '-' }}</td>
                                <td>{{ optional($surat->detail)->alat_angkutan ?? '-' }}</td>
                                <td>{{ $surat->nomor_surat }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data ditemukan.</td>
                            </tr>
                        @endforelse
                    </table>

                    <div class="d-flex justify-content-center">
                        {{ $suratTugasList->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="exportForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Format Export</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Silakan pilih format export data rekap surat tugas:</p>

                        {{-- Hidden Inputs untuk filter --}}
                        <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                        <input type="hidden" name="nama" value="{{ request('nama') }}">
                        <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                        <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">

                        <div class="d-flex justify-content-around">
                            <button type="submit" formaction="{{ route('rekap.exportPdf') }}" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="submit" formaction="{{ route('rekap.exportExcel') }}" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
