@extends('adminlte::page')

@section('title', 'Detail Surat Tugas')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Surat Tugas</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Informasi Surat Tugas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nomor Surat</span>
                                    <span class="info-box-number">{{ $surat->nomor_surat }}</span>
                                </div>
                            </div>
                            
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-warning"><i class="fas fa-tag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Jenis Surat</span>
                                    <span class="info-box-number">{{ ucfirst($surat->jenis) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success"><i class="fas fa-road"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Jarak</span>
                                    <span class="info-box-number">{{ str_replace('_', ' ', ucfirst($surat->jarak)) }}</span>
                                </div>
                            </div>
                            
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-{{ $surat->status === 'disetujui' ? 'success' : ($surat->status === 'ditolak' ? 'danger' : 'warning') }}">
                                    <i class="fas fa-{{ $surat->status === 'disetujui' ? 'check' : ($surat->status === 'ditolak' ? 'times' : 'clock') }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">{{ ucfirst($surat->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Kegiatan Section -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Kegiatan</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Kegiatan/Maksud:</strong><br>
                                            {{ $surat->detail->kegiatan_maksud }}</p>
                                            
                                            <p><strong>Tempat:</strong><br>
                                            {{ $surat->detail->tempat }}</p>
                                            
                                            @if($surat->jarak === 'luar_kota')
                                            <p><strong>Alat Angkutan:</strong><br>
                                            {{ $surat->detail->alat_angkutan ?? '-' }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Tanggal Mulai:</strong><br>
                                            {{ date('d M Y', strtotime($surat->detail->tanggal_mulai)) }}</p>
                                            
                                            <p><strong>Tanggal Selesai:</strong><br>
                                            {{ date('d M Y', strtotime($surat->detail->tanggal_selesai)) }}</p>
                                            
                                            @if($surat->jenis === 'tim')
                                            <p><strong>Lama Perjalanan:</strong><br>
                                            {{ $surat->detail->lama_perjalanan }} hari</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($surat->jarak === 'luar_kota')
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <p><strong>Kota Keberangkatan:</strong><br>
                                            {{ $surat->detail->kota_keberangkatan }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Kota Tujuan:</strong><br>
                                            {{ $surat->detail->kota_tujuan }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Penerima Tugas Section -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Penerima Tugas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Pegawai Utama</h5>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="mr-3">
                                                    <img src="{{ asset('assets/img/avatar.jpg') }}" alt="Profile" class="img-circle img-size-50">
                                                </div>
                                                <div>
                                                    <h5 class="mb-0">{{ $surat->detail->pegawai->gelar_dpn ?? '' }}{{ $surat->detail->pegawai->gelar_dpn ? ' ' : '' }}{{ $surat->detail->pegawai->nama }}{{ $surat->detail->pegawai->gelar_blk ? ', ' . $surat->detail->pegawai->gelar_blk : '' }}</h5>
                                                    <p class="text-muted mb-0">{{ $surat->detail->pegawai->id_staff }}</p>
                                                    <p class="text-muted mb-0">NIP: {{ $surat->detail->pegawai->nip }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($surat->anggota && $surat->anggota->count() > 0)
                                        <div class="col-md-6">
                                            <h5>Pengikut</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama</th>
                                                            <th>Jabatan</th>
                                                            <th>NIP</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($surat->anggota as $index => $anggota)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $anggota->pegawai->gelar_dpn ?? '' }}{{ $anggota->pegawai->gelar_dpn ? ' ' : '' }}{{ $anggota->pegawai->nama }}{{ $anggota->pegawai->gelar_blk ? ', ' . $anggota->pegawai->gelar_blk : '' }}</td>
                                                            <td>{{ $anggota->pegawai->id_staff }}</td>
                                                            <td>{{ $anggota->pegawai->nip }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Approval Section -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Persetujuan Wadir 2</h3>
                                </div>
                                <div class="card-body">
                                    @if($surat->tanggal_disetujui_wadir2)
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <img src="{{ asset('assets/img/avatar.jpg') }}" alt="Profile" class="img-circle img-size-50">
                                            </div>
                                            <div>
                                                <h5 class="mb-0">{{ $surat->pejabat->pegawai->gelar_dpn ?? '' }}{{ $surat->pejabat->pegawai->gelar_dpn ? ' ' : '' }}{{ $surat->pejabat->pegawai->nama }}{{ $surat->pejabat->pegawai->gelar_blk ? ', ' . $surat->pejabat->pegawai->gelar_blk : '' }}</h5>
                                                <p class="text-success mb-0"><i class="fas fa-check-circle"></i> Disetujui pada: {{ date('d M Y', strtotime($surat->tanggal_disetujui_wadir2)) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-warning"><i class="fas fa-clock"></i> Belum disetujui</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Persetujuan Direktur</h3>
                                </div>
                                <div class="card-body">
                                    @if($surat->tanggal_disetujui_pimpinan)
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <img src="{{ asset('assets/img/avatar.jpg') }}" alt="Profile" class="img-circle img-size-50">
                                            </div>
                                            <div>
                                                <h5 class="mb-0">{{ $surat->pimpinan->pegawai->gelar_dpn ?? '' }}{{ $surat->pimpinan->pegawai->gelar_dpn ? ' ' : '' }}{{ $surat->pimpinan->pegawai->nama }}{{ $surat->pimpinan->pegawai->gelar_blk ? ', ' . $surat->pimpinan->pegawai->gelar_blk : '' }}</h5>
                                                <p class="text-success mb-0"><i class="fas fa-check-circle"></i> Disetujui pada: {{ date('d M Y', strtotime($surat->tanggal_disetujui_pimpinan)) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-warning"><i class="fas fa-clock"></i> Belum disetujui</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('surattugas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                
                                @if (auth()->user()->role_aktif === 'wadir2' && !$surat->tanggal_disetujui_wadir2)
                                    <form method="POST" action="{{ route('surattugas.approve', $surat->access_token) }}">
                                        @csrf
                                        <button class="btn btn-primary">
                                            <i class="fas fa-check"></i> Setujui Sebagai Wadir 2
                                        </button>
                                    </form>
                                @elseif(auth()->user()->role_aktif === 'direktur')
                                    @if ($surat->tanggal_disetujui_wadir2 && !$surat->tanggal_disetujui_pimpinan)
                                        <form method="POST" action="{{ route('surattugas.approve', $surat->access_token) }}">
                                            @csrf
                                            <button class="btn btn-primary">
                                                <i class="fas fa-check"></i> Setujui Sebagai Direktur
                                            </button>
                                        </form>
                                    @elseif(!$surat->tanggal_disetujui_wadir2)
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-info-circle"></i> Menunggu persetujuan dari Wadir 2
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box {
            min-height: 80px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: .25rem;
        }
        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            font-size: 1.8rem;
        }
        .info-box-content {
            padding: 10px;
        }
        .info-box-text {
            font-size: .9rem;
            color: #6c757d;
        }
        .info-box-number {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .img-size-50 {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .card-outline {
            border-top: 3px solid #17a2b8 !important;
        }
    </style>
@stop