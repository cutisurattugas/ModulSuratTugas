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
                        {{ date('d M Y', strtotime($item->tanggal_mulai)) }} - {{ date('d M Y', strtotime($item->tanggal_selesai)) }}
                    @else
                        {{ date('d M Y', strtotime($item->tanggal_berangkat)) }} - {{ date('d M Y', strtotime($item->tanggal_kembali)) }}
                    @endif
                </td>

                {{-- Opsi --}}
                <td class="text-center">
                    @if ($mode === 'kelompok')
                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPelaksana{{ $item->id }}">
                            <i class="fas fa-users"></i>
                        </button>
                    @endif
                    <a class="btn btn-warning btn-sm" href="#">
                        <i class="nav-icon fas fa-edit"></i>
                    </a>
                </td>
            </tr>

            {{-- Modal Pelaksana (hanya untuk kelompok) --}}
            @if ($mode === 'kelompok')
                <div class="modal fade" id="modalPelaksana{{ $item->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel{{ $item->id }}">Daftar Pelaksana</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
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
                                            <li>{{ $pengikut->pegawai->gelar_dpn ?? '' }}{{ $pengikut->pegawai->gelar_dpn ? ' ' : '' }}{{ $pengikut->pegawai->nama }}{{ $pengikut->pegawai->gelar_blk ? ', ' . $pengikut->pegawai->gelar_blk : '' }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data dinas luar {{ $mode }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
