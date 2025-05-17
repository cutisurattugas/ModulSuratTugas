<table class="table table-bordered">
    <thead>
        <tr>
            <th width="1%">No</th>
            <th class="text-center">Nama Pegawai</th>
            <th class="text-center">Tujuan / Kegiatan</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Opsi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($dinas_data as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>

                {{-- Nama Pegawai --}}
                <td class="text-center">
                    {{ $item->pegawai->gelar_dpn ?? '' }}{{ $item->pegawai->gelar_dpn ? ' ' : '' }}{{ $item->pegawai->nama }}{{ $item->pegawai->gelar_blk ? ', ' . $item->pegawai->gelar_blk : '' }}
                    @if ($mode == 'kelompok' && $item->pengikut->count())
                        <br>
                        <small class="text-muted">
                            +{{ $item->pengikut->count() }} pengikut
                        </small>
                    @endif
                </td>

                {{-- Tujuan / Kegiatan --}}
                <td class="text-center">
                    {{ $mode === 'individu' ? $item->tempat : $item->maksud }}
                </td>

                {{-- Tanggal --}}
                <td class="text-center">
                    @if ($mode === 'individu')
                        {{ date('d M Y', strtotime($item->tanggal_mulai)) }} -
                        {{ date('d M Y', strtotime($item->tanggal_selesai)) }}
                    @else
                        {{ date('d M Y', strtotime($item->tanggal_berangkat)) }} -
                        {{ date('d M Y', strtotime($item->tanggal_kembali)) }}
                    @endif
                </td>

                {{-- Opsi --}}
                <td class="text-center">
                    @if ($mode === 'kelompok')
                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalPelaksana{{ $item->id }}">
                            <i class="fas fa-users"></i>
                        </button>
                    @endif

                    @if (auth()->user()->role_aktif === 'pegawai' || auth()->user()->role_aktif === 'dosen' || auth()->user()->role_aktif === 'direktur' || auth()->user()->role_aktif === 'wadir1' || auth()->user()->role_aktif === 'wadir2' || auth()->user()->role_aktif === 'wadir3')
                        @php
                            $laporan = Modules\SuratTugas\Entities\LaporanPerjalananDinas::where(
                                'perjalanan_dinas_id',
                                $item->perjalanan->id,
                            )->first();
                        @endphp

                        @if ($laporan)
                            <a href="{{ Storage::url($laporan->file_laporan) }}" class="btn btn-info btn-sm"
                                target="_blank">
                                <i class="nav-icon fas fa-file-pdf"></i> {{ basename($laporan->file_laporan) }}
                            </a>
                        @else
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalUploadLaporan{{ $item->perjalanan->access_token }}">
                                <i class="nav-icon fas fa-arrow-circle-up"></i>
                            </button>
                        @endif
                        <a class="btn btn-success btn-sm" href="#">
                            <i class="nav-icon fas fa-print"></i>
                        </a>
                    @endif
                    @if (auth()->user()->role_aktif === 'admin' && isset($item->perjalanan->access_token))
                        <a href="{{ route('perjadin.edit', $item->perjalanan->access_token) }}" class="btn btn-warning btn-sm">
                            <i class="nav-icon fas fa-edit"></i>
                        </a>
                    @endif
                </td>
            </tr>

            {{-- Modal Pelaksana (hanya untuk kelompok) --}}
            @if ($mode === 'kelompok')
                <div class="modal fade" id="modalPelaksana{{ $item->id }}" tabindex="-1"
                    aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel{{ $item->id }}">Daftar Pelaksana</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <strong>Pegawai Utama:</strong>
                                <p>
                                    {{ $item->pegawai->gelar_dpn ?? '' }}{{ $item->pegawai->gelar_dpn ? ' ' : '' }}{{ $item->pegawai->nama }}{{ $item->pegawai->gelar_blk ? ', ' . $item->pegawai->gelar_blk : '' }}
                                </p>

                                <strong>Pengikut:</strong>
                                @if ($item->pengikut->isEmpty())
                                    <p class="text-muted">Tidak ada pengikut.</p>
                                @else
                                    <ul>
                                        @foreach ($item->pengikut as $pengikut)
                                            <li>{{ $pengikut->pegawai->gelar_dpn ?? '' }}{{ $pengikut->pegawai->gelar_dpn ? ' ' : '' }}{{ $pengikut->pegawai->nama }}{{ $pengikut->pegawai->gelar_blk ? ', ' . $pengikut->pegawai->gelar_blk : '' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Modal Upload Laporan -->
            <div class="modal fade" id="modalUploadLaporan{{ $item->perjalanan->access_token }}" tabindex="-1"
                aria-labelledby="modalUploadLaporanLabel{{ $item->perjalanan->access_token }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('perjadin.upload', $item->perjalanan->access_token) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalUploadLaporanLabel{{ $item->perjalanan->access_token }}">Unggah
                                    Laporan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="file_laporan{{ $item->perjalanan->access_token }}" class="form-label">Pilih File
                                        Laporan</label>
                                    <input type="file" name="file_laporan" class="form-control"
                                        id="file_laporan{{ $item->perjalanan->access_token }}" required>
                                    <small class="text-muted">Format: PDF, DOCX | Max: 10MB</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Unggah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data dinas luar {{ $mode }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
