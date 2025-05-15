<table class="table table-bordered">
    <thead>
        <tr>
            <th width="1%">No</th>
            <th><center>Nama</center></th>
            <th><center>Tujuan</center></th>
            <th><center>Tanggal</center></th>
            <th><center>Status</center></th>
            <th><center>Opsi</center></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($dinas_data as $item)
            <tr>
                <td><center>{{ $loop->iteration }}</center></td>
                <td><center>{{ $item->pegawai->nama }}</center></td>
                <td><center>{{ $item->tujuan }}</center></td>
                <td><center>{{ date('d M Y', strtotime($item->tanggal_mulai)) }} - {{ date('d M Y', strtotime($item->tanggal_selesai)) }}</center></td>
                <td>
                    <center>
                        @php
                            $status = $item->status;
                            switch ($status) {
                                case 'Diajukan': $badgeClass = 'secondary'; break;
                                case 'Diproses': $badgeClass = 'info'; break;
                                case 'Disetujui': $badgeClass = 'primary'; break;
                                case 'Selesai': $badgeClass = 'success'; break;
                                case 'Ditolak': $badgeClass = 'danger'; break;
                                default: $badgeClass = 'light';
                            }
                        @endphp
                        <span class="badge rounded-pill bg-{{ $badgeClass }}">{{ $status }}</span>
                    </center>
                </td>
                <td>
                    <center>
                        <a class="btn btn-info btn-sm" href="#">
                            <i class="nav-icon fas fa-eye"></i>
                        </a>
                        <a class="btn btn-warning btn-sm" href="#">
                            <i class="nav-icon fas fa-edit"></i>
                        </a>
                    </center>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada data dinas luar {{ $mode }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
