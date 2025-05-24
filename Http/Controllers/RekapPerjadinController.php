<?php

namespace Modules\SuratTugas\Http\Controllers;

use Modules\SuratTugas\Exports\SuratTugasExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\SuratTugas\Entities\SuratTugas;

class RekapPerjadinController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        // Ambil tahun aktif dari request atau default ke tahun ini
        $tahun = $request->input('tahun', date('Y'));

        // Ambil semua tahun untuk dropdown filter
        $daftarTahun = range($tahun - 5, $tahun + 1); // 5 tahun lalu hingga sekarang

        // Query dasar
        $query = SuratTugas::with([
            'detail' => function ($q) use ($tahun) {
                $q->with('pegawai');
                $q->whereYear('tanggal_mulai', $tahun);
            },
            'pejabat.pegawai'
        ]);

        // Filter berdasarkan nama pegawai
        if ($request->filled('nama')) {
            $nama = $request->input('nama');
            $query->whereHas('detail.pegawai', function ($q) use ($nama) {
                $q->where('nama', 'like', "%$nama%");
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereHas('detail', function ($q) use ($request) {
                $q->whereBetween('tanggal_mulai', [$request->tanggal_awal, $request->tanggal_akhir]);
            });
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereHas('detail', function ($q) use ($request) {
                $q->whereDate('tanggal_mulai', '>=', $request->tanggal_awal);
            });
        } elseif ($request->filled('tanggal_akhir')) {
            $query->whereHas('detail', function ($q) use ($request) {
                $q->whereDate('tanggal_selesai', '<=', $request->tanggal_akhir);
            });
        }

        // Paginate hasil
        $suratTugasList = $query->latest()->paginate(10);

        return view('surattugas::rekap_perjadin.index', compact('suratTugasList', 'daftarTahun', 'tahun'));
    }

    public function exportPdf(Request $request)
    {
        // Ambil filter
        $tahun = $request->input('tahun');
        $nama = $request->input('nama');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Query dasar dengan relasi detail & pegawai
        $query = SuratTugas::whereHas('detail')
            ->with(['detail.pegawai', 'pejabat.pegawai']);

        // Filter berdasarkan nama pegawai
        if ($nama) {
            $query->whereHas('detail.pegawai', function ($q) use ($nama) {
                $q->where('nama', 'like', "%$nama%");
            });
        }

        // Filter berdasarkan tanggal
        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereHas('detail', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                $q->whereBetween('tanggal_mulai', [$tanggalAwal, $tanggalAkhir]);
            });
        } elseif ($tanggalAwal) {
            $query->whereHas('detail', function ($q) use ($tanggalAwal) {
                $q->whereDate('tanggal_mulai', '>=', $tanggalAwal);
            });
        } elseif ($tanggalAkhir) {
            $query->whereHas('detail', function ($q) use ($tanggalAkhir) {
                $q->whereDate('tanggal_selesai', '<=', $tanggalAkhir);
            });
        } else {
            $query->whereYear('created_at', $tahun ?? now()->year);
        }

        $data = $query->get();

        return Pdf::loadView('surattugas::rekap_perjadin.pdf', compact('data', 'tahun', 'tanggalAwal', 'tanggalAkhir'))
            ->stream("rekap_perjadin_" . now()->format('YmdHis') . ".pdf");
    }

    public function exportExcel(Request $request)
    {
        // Ambil filter
        $tahun = $request->input('tahun');
        $nama = $request->input('nama');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Siapkan query
        $query = SuratTugas::whereHas('detail')
            ->with(['detail.pegawai', 'pejabat.pegawai']);

        if ($nama) {
            $query->whereHas('detail.pegawai', function ($q) use ($nama) {
                $q->where('nama', 'like', "%$nama%");
            });
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereHas('detail', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                $q->whereBetween('tanggal_mulai', [$tanggalAwal, $tanggalAkhir]);
            });
        } elseif ($tanggalAwal) {
            $query->whereHas('detail', function ($q) use ($tanggalAwal) {
                $q->whereDate('tanggal_mulai', '>=', $tanggalAwal);
            });
        } elseif ($tanggalAkhir) {
            $query->whereHas('detail', function ($q) use ($tanggalAkhir) {
                $q->whereDate('tanggal_selesai', '<=', $tanggalAkhir);
            });
        } else {
            $query->whereYear('created_at', $tahun ?? now()->year);
        }

        $data = $query->get();

        return Excel::download(new SuratTugasExport($data), "rekap_surat_tugas_" . now()->format('YmdHis') . ".xlsx");
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('surattugas::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('surattugas::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
