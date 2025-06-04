<table class="table table-bordered">
    <thead>
        <tr>
            <th width="1%">No</th>
            @if ($mode == 'semua')
                <th class="text-center">Jenis Surat</th>
            @endif
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

                @if ($mode == 'semua')
                    <td class="text-center text-uppercase">{{ $item->jenis }}</td>
                @endif

                {{-- Nama Pegawai --}}
                <td class="text-center">
                    {{ $item->detail->pegawai->gelar_dpn ?? '' }}{{ $item->detail->pegawai->gelar_dpn ? ' ' : '' }}{{ $item->detail->pegawai->nama }}{{ $item->detail->pegawai->gelar_blk ? ', ' . $item->detail->pegawai->gelar_blk : '' }}
                    @if ($mode == 'kelompok' || ($mode == 'semua' && $item->jenis == 'tim'))
                        @if ($item->anggota->count())
                            <br>
                            <small class="text-muted">
                                +{{ $item->anggota->count() }} anggota
                            </small>
                        @endif
                    @endif
                </td>

                {{-- Tujuan / Kegiatan --}}
                <td class="text-center">
                    <a href="{{ route('surattugas.scan', $item->access_token) }}"
                        style="color: black">{{ $item->detail->kegiatan_maksud }}</a>
                    @if ($item->jarak == 'luar_kota' && $item->detail->alat_angkutan)
                        <br><small class="text-muted">Angkutan: {{ $item->detail->alat_angkutan }}</small>
                    @endif
                </td>

                {{-- Tanggal --}}
                <td class="text-center">
                    {{ date('d M Y', strtotime($item->detail->tanggal_mulai)) }} -
                    {{ date('d M Y', strtotime($item->detail->tanggal_selesai)) }}
                    @if ($mode == 'kelompok' || ($mode == 'semua' && $item->jenis == 'tim'))
                        <br><small class="text-muted">({{ $item->detail->lama_perjalanan }} hari)</small>
                    @endif
                </td>

                {{-- Opsi --}}
                <td class="text-center">
                    <!-- Opsi tombol tetap sama seperti sebelumnya -->
                    @if ($item->tanggal_disetujui_wadir2 && $item->tanggal_disetujui_pimpinan)
                        <a class="btn btn-success btn-sm" href="{{ route('surattugas.print', $item->access_token) }}">
                            <i class="nav-icon fas fa-print"></i>
                        </a>
                    @endif

                    @php
                        $rolesCanUpload = ['pegawai', 'dosen'];
                        $rolesCanViewLaporan = ['pegawai', 'dosen', 'wadir2', 'direktur'];
                    @endphp

                    {{-- Tombol Lihat Surat Tugas --}}
                    @if (in_array(auth()->user()->role_aktif, ['direktur', 'wadir2']))
                        <a href="{{ route('surattugas.show', $item->access_token) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    @endif

                    {{-- Tombol Lihat atau Upload Laporan --}}
                    @if (in_array(auth()->user()->role_aktif, $rolesCanUpload))
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalUploadLaporan{{ $item->access_token }}">
                            <i class="nav-icon fas fa-arrow-circle-up"></i>
                        </button>
                    @endif

                    @if ($item->laporan && in_array(auth()->user()->role_aktif, $rolesCanViewLaporan))
                        <a href="{{ Storage::url($item->laporan->file_laporan) }}" class="btn btn-info btn-sm"
                            target="_blank">
                            <i class="nav-icon fas fa-file-pdf"></i> Laporan
                        </a>
                    @endif

                    @if (auth()->user()->role_aktif === 'admin')
                        <a href="{{ route('surattugas.edit', $item->access_token) }}" class="btn btn-warning btn-sm">
                            <i class="nav-icon fas fa-edit"></i>
                        </a>
                    @endif
                </td>
            </tr>

            <!-- Modal Upload Laporan -->
            <div class="modal fade" id="modalUploadLaporan{{ $item->access_token }}" tabindex="-1"
                aria-labelledby="modalUploadLaporanLabel{{ $item->access_token }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('surattugas.upload', $item->access_token) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalUploadLaporanLabel{{ $item->access_token }}">Unggah
                                    Laporan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="file_laporan{{ $item->access_token }}" class="form-label">Pilih File
                                        Laporan</label>
                                    <input type="file" name="file_laporan" class="form-control"
                                        id="file_laporan{{ $item->access_token }}" accept=".pdf" required>
                                    <small class="text-muted">Format: PDF | Max: 10MB</small>
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
                <td colspan="{{ $mode == 'semua' ? 6 : 5 }}" class="text-center text-muted">Belum ada data surat tugas
                    {{ $mode }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
