@extends('adminlte::page')
@section('title', 'Surat Tugas')

@section('content_header')
    <h1 class="m-0 text-dark">Surat Tugas Kepanitiaan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th width="1%">No</th>
                                <th class="text-center">Nama Kepanitiaan</th>
                                <th class="text-center">Pimpinan Kepanitiaan</th>
                                <th class="text-center">Tanggal Mulai</th>
                                <th class="text-center">Tanggal Berakhir</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kepanitiaans as $index => $suratTugasKepanitiaan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $suratTugasKepanitiaan->nama_kepanitiaan }}</td>
                                    <td>{{ $suratTugasKepanitiaan->gelar_dpn ?? '' . ' ' . ucwords(strtolower($suratTugasKepanitiaan->nama)) . ' ' . $suratTugasKepanitiaan->gelar_blk }}
                                    </td>
                                    <td>
                                        {{ date('d M Y', strtotime($suratTugasKepanitiaan->tanggal_mulai)) }}
                                    </td>
                                    <td>
                                        {{ date('d M Y', strtotime($suratTugasKepanitiaan->tanggal_berakhir)) }}
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-{{ $suratTugasKepanitiaan->status === 'AKTIF' ? 'success' : 'danger' }}">{{ $suratTugasKepanitiaan->status }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('/rapat/panitia/download/' . $suratTugasKepanitiaan->slug) }}"
                                            target="_blank">Unduh Surat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak Ada Kepanitiaan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
