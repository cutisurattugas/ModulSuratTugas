<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Surat Tugas</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0.5in;
            color: #000;
            line-height: 1.4;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }

        h2 {
            text-align: center;
            font-size: 14pt;
            margin: 5px 0;
        }

        h6 {
            text-align: center;
            font-size: 10pt;
        }

        .footer {
            margin-top: 30px;
            font-size: 9pt;
            text-align: right;
        }
    </style>
</head>
<body>

<h2>REKAP SURAT TUGAS</h2>
<h6>Tahun: {{ $tahun ?? now()->year }}</h6>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pegawai</th>
            <th>Jenis</th>
            <th>Kegiatan</th>
            <th>Tanggal</th>
            <th>Kota Tujuan</th>
            <th>Alat Angkutan</th>
            <th>Nomor Surat</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $surat)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional(optional($surat->detail)->pegawai)->nama ?? '-' }}</td>
                <td>{{ ucfirst($surat->jenis) }}</td>
                <td>{{ optional($surat->detail)->kegiatan_maksud ?? '-' }}</td>
                <td>
                    {{ optional($surat->detail)->tanggal_mulai ? date('d M Y', strtotime($surat->detail->tanggal_mulai)) : '-' }}
                    -
                    {{ optional($surat->detail)->tanggal_selesai ? date('d M Y', strtotime($surat->detail->tanggal_selesai)) : '-' }}
                </td>
                <td>{{ optional($surat->detail)->kota_tujuan ?? '-' }}</td>
                <td>{{ optional($surat->detail)->alat_angkutan ?? '-' }}</td>
                <td>{{ $surat->nomor_surat }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Dicetak pada: {{ now()->format('d M Y H:i:s') }}
</div>

</body>
</html>