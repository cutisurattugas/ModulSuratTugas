<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Perjalanan Dinas - Individu {{ $perjalanan->access_token }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0.5in;
            color: #000;
            background: #f4f4f4;
            line-height: 1.2;
            font-size: 10pt;
            box-sizing: border-box;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            /* Logo akan rata kiri */
        }

        .kop-surat img {
            width: 90px;
            height: auto;
            margin-right: 15px;
        }

        .kop-surat-text {
            text-align: center;
            font-size: 10pt;
        }

        h2 {
            font-size: 14pt;
            text-align: center;
            margin: 10px 0;
        }

        table.form {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }

        table.form td {
            padding: 3px 6px;
            vertical-align: top;
            white-space: nowrap;
            width: 30%;
        }

        table.form td+td {
            width: 70%;
            font-weight: normal;
        }

        .container {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .table-cuti {
            width: 45%;
        }

        .signatures {
            width: 55%;
        }

        .ttd {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sign {
            text-align: center;
        }

        .catatan-kepegawaian {
            margin-top: 10px;
            font-size: 9pt;
        }

        /* Tombol Print hanya muncul di web */
        .web-only {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-print {
            display: inline-block;
            padding: 5px 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 9pt;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .digital-stamp {
            display: flex;
            align-items: center;
            border: 1px solid #000;
            padding: 2px 4px;
            max-width: 260px;
            font-size: 7pt;
            line-height: 1.1;
            margin: 5px auto 0 auto;
            background-color: white;
        }

        .stamp-logo {
            flex-shrink: 0;
            margin-right: 6px;
        }

        .stamp-logo img {
            width: 50px;
            height: auto;
        }

        .stamp-text {
            flex-grow: 1;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            font-size: 9pt;
            page-break-inside: avoid;
        }

        .signature-info {
            flex-grow: 1;
            margin-left: 10px;
        }

        /* Styling tambahan untuk Preview Web */
        @media screen {
            body {
                display: flex;
                justify-content: center;
                background-color: #f4f4f4;
            }

            .page-wrapper {
                width: 100%;
                max-width: 8.5in;
                background-color: white;
                padding: 20px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                border: 1px solid #ddd;
                margin-top: 30px;
                margin-bottom: 30px;
            }
        }

        /* Styling untuk Print */
        @media print {
            .web-only {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .page-wrapper {
                box-shadow: none;
                padding-top: 0.5in;
                padding-left: 0.5in;
                padding-right: 0.5in;
                height: 100vh;
                position: relative;
                background: white;
                box-sizing: border-box;
            }

            .signatures {
                page-break-inside: avoid;
            }

            .footer {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 0 0.5in 0.3in 0.5in;
                font-size: 9pt;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>

    <!-- Tombol hanya muncul di browser -->
    <div class="page-wrapper">

        <!-- Tombol Print Preview -->
        <div class="web-only">
            <button class="btn-print" onclick="window.print()">🖨️ Tampilkan Print Preview</button>
        </div>

        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Politeknik Negeri Banyuwangi">
            <div class="kop-surat-text">
                <strong>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET DAN TEKNOLOGI</strong><br>
                <strong>POLITEKNIK NEGERI BANYUWANGI</strong><br>
                Jalan Raya Jember KM 13 Labanasem Kabat-Banyuwangi, 68461<br>
                Telp/Fax: (0333) 636780; E-mail: poliwangi@poliwangi.ac.id; Laman: poliwangi.ac.id
            </div>
        </div>

        <hr style="margin: 10px 0;">

        <h2>Surat Tugas</h2>
        <p style="font-size: 10pt;
            text-align: center;
            margin: 10px 0;">Nomor:
            {{ $perjalanan->nomor_surat }}</p>

        <p>Yang bertanda tangan dibawah ini, Direktur Politeknik Negeri Banyuwangi
            menugaskan Pegawai sebagai
            berikut:</p>

        @if ($perjalanan->anggota && count($perjalanan->anggota) > 0)
            <table class="form" border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead style="text-align: center;">
                    <tr>
                        <th style="width: 1%;">No</th>
                        <th>Nama</th>
                        <th>NIP / NIPPPK / NIK</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Baris Ketua Tim --}}
                    @php $ketua = $perjalanan->detail->pegawai; @endphp
                    <tr>
                        <th style="width: 1%;">1</th>
                        <td>
                            {{ $ketua->gelar_dpn ? $ketua->gelar_dpn . ' ' : '' }}
                            {{ $ketua->nama }}
                            {{ $ketua->gelar_blk ? ', ' . $ketua->gelar_blk : '' }}
                        </td>
                        <td><center>{{ $ketua->nip ?? ($ketua->nipppk ?? ($ketua->nik ?? '-')) }}</center></td>
                        <td>{{ $ketua->id_staff }}</td>
                    </tr>

                    {{-- Baris Anggota Tim --}}
                    @foreach ($perjalanan->anggota as $index => $item)
                        @php $pegawai = $item->pegawai; @endphp
                        <tr>
                            <th style="width: 1%;">{{ $index + 2 }}</th>
                            <td>
                                {{ $pegawai->gelar_dpn ? $pegawai->gelar_dpn . ' ' : '' }}
                                {{ $pegawai->nama }}
                                {{ $pegawai->gelar_blk ? ', ' . $pegawai->gelar_blk : '' }}
                            </td>
                            <td><center>{{ $pegawai->nip ?? ($pegawai->nipppk ?? ($pegawai->nik ?? '-')) }}</center></td>
                            <td>{{ $pegawai->id_staff }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="form">
                <tr>
                    <td>Nama</td>
                    <td>:
                        {{ $perjalanan->detail->pegawai->gelar_dpn ?? '' }}{{ $perjalanan->detail->pegawai->gelar_dpn ? ' ' : '' }}{{ $perjalanan->detail->pegawai->nama }}{{ $perjalanan->detail->pegawai->gelar_blk ? ', ' . $perjalanan->detail->pegawai->gelar_blk : '' }}
                    </td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>: {{ $perjalanan->detail->pegawai->nip }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: {{ $perjalanan->detail->pegawai->id_staff }}</td>
                </tr>
            </table>
        @endif

        <p>ditugaskan untuk mengikuti:</p>

        <table class="form">
            <tr>
                <td>Kegiatan</td>
                <td>:
                    {{ $perjalanan->detail->kegiatan_maksud }}
                </td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>: {{ date('d M Y', strtotime($perjalanan->detail->tanggal_mulai)) }} -
                    {{ date('d M Y', strtotime($perjalanan->detail->tanggal_selesai)) }}</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>: {{ $perjalanan->detail->tempat }}</td>
            </tr>
        </table>

        <p>Demikian Surat Tugas ini untuk dilaksanakan dengan penuh tanggung jawab, serta dipersipkan dengan
            sebaik-baiknya.</p>

        <div class="container">
            <!-- Tabel Cuti -->
            <div class="table-cuti">

            </div>

            <!-- Kolom Tanda Tangan Bertiga Vertikal -->
            <div class="signatures">
                <div class="ttd">
                    <div class="sign">
                        Banyuwangi, {{ date('d M Y', strtotime($perjalanan->created_at)) }}<br>
                        {{ $direktur->jabatan->jabatan }},<br>
                        <div class="digital-stamp">
                            <div class="stamp-logo">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Instansi">
                            </div>
                            <div class="stamp-text">
                                Ditandatangani secara elektronik oleh<br>
                                Direktur Politeknik Negeri Banyuwangi<br>
                                selaku Pejabat yang Berwenang
                            </div>
                            <div>
                                <img src="data:image/svg+xml;base64,{{ base64_encode($qrCodeImage) }}" alt="QR Code"
                                    style="width: 45px; height: 45px;" />
                            </div>
                        </div>
                        {{ $direktur->pegawai->gelar_dpn ?? '' }}{{ $direktur->pegawai->gelar_dpn ? ' ' : '' }}{{ $direktur->pegawai->nama }}{{ $direktur->pegawai->gelar_blk ? ', ' . $direktur->pegawai->gelar_blk : '' }}
                        <br>
                        NIP. {{ $direktur->pegawai->nip }}
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- Tutup .page-wrapper -->
    
</body>

</html>
