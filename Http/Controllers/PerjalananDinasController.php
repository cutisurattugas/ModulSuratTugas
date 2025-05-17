<?php

namespace Modules\SuratTugas\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Pengaturan\Entities\Pegawai;
use Modules\Pengaturan\Entities\Pejabat;
use Modules\SuratTugas\Entities\PengikutPerjalananDinas;
use Modules\SuratTugas\Entities\PerjalananDinas;
use Modules\SuratTugas\Entities\PerjalananDinasIndividu;
use Modules\SuratTugas\Entities\PerjalananDinasTim;

class PerjalananDinasController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $individu = PerjalananDinasIndividu::with(['pegawai', 'perjalanan'])->latest()->get();
        $tim = PerjalananDinasTim::with(['pegawai', 'perjalanan', 'pengikut'])->latest()->get();

        return view('surattugas::dinas_luar.index', [
            'dinas_individu' => $individu,
            'dinas_tim' => $tim,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $pejabat = Pejabat::all();
        $pegawai = Pegawai::all();
        return view('surattugas::dinas_luar.create', compact('pejabat', 'pegawai'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // 1. Validasi data utama
        $request->validate([
            'nomor_surat' => 'required|unique:perjalanan_dinas,nomor_surat',
            'pejabat_id' => 'required|exists:pejabats,id',
            'jenis' => 'required|in:individu,tim',
        ]);

        DB::beginTransaction();

        try {
            // 2. Simpan data utama
            $perjalanan = PerjalananDinas::create([
                'nomor_surat' => $request->nomor_surat,
                'pejabat_id' => $request->pejabat_id,
                'jenis' => $request->jenis,
            ]);

            // 3. Simpan data detail berdasarkan jenis
            if ($request->jenis === 'individu') {
                // Validasi tambahan
                $request->validate([
                    'pegawai_id' => 'required|exists:pegawais,id',
                    'kegiatan' => 'required|string',
                    'tanggal' => 'required|string', // bentuknya "YYYY-MM-DD to YYYY-MM-DD"
                    'tempat' => 'required|string',
                ]);

                // Pecah tanggal range
                $tanggalRange = explode(' to ', $request->tanggal);
                $tanggalMulai = $tanggalRange[0] ?? null;
                $tanggalSelesai = $tanggalRange[1] ?? $tanggalRange[0];

                // Simpan ke tabel individu
                PerjalananDinasIndividu::create([
                    'perjalanan_dinas_id' => $perjalanan->id,
                    'pegawai_id' => $request->pegawai_id,
                    'kegiatan' => $request->kegiatan,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'tempat' => $request->tempat,
                ]);
            } else {
                // Validasi tambahan
                $request->validate([
                    'pegawai_id' => 'required|exists:pegawais,id',
                    'maksud' => 'required|string',
                    'alat_angkutan' => 'required|string',
                    'lama_perjalanan' => 'required|integer|min:1',
                    'tanggal' => 'required|string',
                ]);

                // Pecah tanggal range
                $tanggalRange = explode(' to ', $request->tanggal);
                $tanggal_berangkat = $tanggalRange[0] ?? null;
                $tanggal_kembali = $tanggalRange[1] ?? $tanggalRange[0];

                // Simpan ke tabel tim
                $tim = PerjalananDinasTim::create([
                    'perjalanan_dinas_id' => $perjalanan->id,
                    'pegawai_id' => $request->pegawai_id,
                    'maksud' => $request->maksud,
                    'alat_angkutan' => $request->alat_angkutan,
                    'lama_perjalanan' => $request->lama_perjalanan,
                    'tanggal_berangkat' => $tanggal_berangkat,
                    'tanggal_kembali' => $tanggal_kembali,
                ]);

                // Simpan pengikut jika ada
                if ($request->has('pengikut_ids') && is_array($request->pengikut_ids)) {
                    foreach ($request->pengikut_ids as $pengikutId) {
                        PengikutPerjalananDinas::create([
                            'perjalanan_dinas_tim_id' => $tim->id,
                            'pegawai_id' => $pengikutId,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Perjalanan dinas berhasil disimpan',
                'data' => $perjalanan
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage(),
            ], 500);
        }
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
