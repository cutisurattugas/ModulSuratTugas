<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Persetujuan Perjalanan Dinas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .progress-bar {
            transition: width 1s ease-in-out;
        }

        .progress-step {
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
        }

        .step-label {
            font-size: 14px;
            margin-top: 5px;
        }

        /* Status dibatalkan */
        .cancelled {
            background-color: #dc3545;
            /* Merah untuk status dibatalkan */
        }

        .cancelled .progress-bar {
            background-color: #dc3545;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container py-5">
        <!-- Header -->
        <div class="text-center mb-5">
            <h2 class="fw-bold">
                Persetujuan Perjalanan Dinas
            </h2>
        </div>

        <!-- Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p><strong>Nama Pegawai:</strong><br>
                            {{ $perjalanan->detail->pegawai->gelar_dpn ?? '' }}{{ $perjalanan->detail->pegawai->gelar_dpn ? ' ' : '' }}{{ $perjalanan->detail->pegawai->nama }}{{ $perjalanan->detail->pegawai->gelar_blk ? ', ' . $perjalanan->detail->pegawai->gelar_blk : '' }}
                        </p>
                        <p><strong>NIP:</strong><br> {{ $perjalanan->detail->pegawai->nip }}</p>
                        <p><strong>Jenis:</strong><br>
                            @if ($perjalanan->jenis == 'individu')
                                <span class="fw-bold text-primary">Individu</span>
                            @elseif($perjalanan->jenis == 'tim')
                                <span class="fw-bold text-primary">Tim</span>
                            @endif
                        </p>
                        <p><strong>Dinas:</strong><br>
                            @if ($perjalanan->jarak == 'dalam_kota')
                                <span class="fw-bold text-primary">Dalam Kota</span>
                            @elseif($perjalanan->jarak == 'luar_kota')
                                <span class="fw-bold text-primary">Luar Kota</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tanggal Penugasan:</strong><br>
                            @if ($perjalanan->detail->tanggal_mulai == $perjalanan->detail->tanggal_selesai)
                                {{ date('d M Y', strtotime($perjalanan->detail->tanggal_mulai)) }}
                            @else
                                {{ date('d M Y', strtotime($perjalanan->detail->tanggal_mulai)) }} s.d
                                {{ date('d M Y', strtotime($perjalanan->detail->tanggal_selesai)) }}
                            @endif

                        </p>
                        <p><strong>Dibuat</strong><br>
                            {{ $perjalanan->created_at ? date('d M Y: H:i:s', strtotime($perjalanan->created_at)) : '-' }}
                        </p>
                        <p><strong>Tanggal Disetujui Wadir 2:</strong><br>
                            {{ date('d M Y: H:i:s', strtotime($perjalanan->tanggal_disetujui_wadir2 ?? '-'))}}
                        </p>
                        <p><strong>Tanggal Disetujui Direktur:</strong><br>
                            {{ date('d M Y: H:i:s', strtotime($perjalanan->tanggal_disetujui_pimpinan ?? '-'))}}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- card 2 --}}
        @if ($perjalanan->anggota && count($perjalanan->anggota) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2>Pengikut</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%;">No</th>
                                <th>
                                    <center>Nama</center>
                                </th>
                                <th>
                                    <center>NIP / NIPPPK / NIK</center>
                                </th>
                                <th>
                                    <center>Jabatan</center>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perjalanan->anggota as $index => $item)
                                @php $pegawai = $item->pegawai; @endphp
                                <tr>
                                    <th style="width: 1%;">{{ $loop->iteration }}</th>
                                    <td>
                                        <center>
                                            {{ $pegawai->gelar_dpn ? $pegawai->gelar_dpn . ' ' : '' }}
                                            {{ $pegawai->nama }}
                                            {{ $pegawai->gelar_blk ? ', ' . $pegawai->gelar_blk : '' }}
                                        </center>
                                    </td>
                                    <td>
                                        <center>{{ $pegawai->nip ?? ($pegawai->nipppk ?? ($pegawai->nik ?? '-')) }}
                                        </center>
                                    </td>
                                    <td>
                                        <center>{{ $pegawai->id_staff }}</center>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
