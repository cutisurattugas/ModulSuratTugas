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
        $user = auth()->user();
        $role = $user->role_aktif;

        // Ambil data pegawai dari user login
        $pegawai = Pegawai::where('username', $user->username)->first();
        $pegawai_id = optional($pegawai)->id;

        // Ambil data pejabat (jika ada)
        $pejabat = Pejabat::where('pegawai_id', $pegawai_id)->first();
        $pejabat_id = optional($pejabat)->id;

        // Variabel awal
        $dinas_individu = collect(); // Data individu
        $dinas_tim = collect();      // Data tim

        if ($role === 'admin') {
            // Admin bisa lihat semua data
            $dinas_individu = PerjalananDinasIndividu::with('pegawai')->latest()->get();
            $dinas_tim = PerjalananDinasTim::with(['pegawai', 'pengikut.pegawai'])->latest()->get();
        } elseif ($role === 'direktur' || $role === 'wadir1' || $role === 'wadir2' || $role === 'wadir3') {
            // PPK hanya bisa lihat surat tugas yang dia buat
            $dinas_individu = PerjalananDinasIndividu::with('pegawai')
                ->whereHas('perjalanan', function ($q) use ($pejabat_id) {
                    $q->where('pejabat_id', $pejabat_id);
                })
                ->latest()->get();

            $dinas_tim = PerjalananDinasTim::with(['pegawai', 'pengikut.pegawai'])
                ->whereHas('perjalanan', function ($q) use ($pejabat_id) {
                    $q->where('pejabat_id', $pejabat_id);
                })
                ->latest()->get();
        } elseif ($role === 'pegawai' || $role === 'dosen' || $role === 'staf') {
            // Pegawai biasa hanya lihat:
            // - Sebagai pegawai di perjalanan individu
            // - Sebagai ketua pelaksana di perjalanan tim (bukan pengikut)

            $dinas_individu = PerjalananDinasIndividu::with('pegawai')
                ->where('pegawai_id', $pegawai_id)
                ->latest()->get();

            $dinas_tim = PerjalananDinasTim::with(['pegawai', 'pengikut.pegawai'])
                ->where('pegawai_id', $pegawai_id)
                ->latest()->get();
        }

        return view('surattugas::dinas_luar.index', compact('dinas_individu', 'dinas_tim'));
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

            return redirect()->route('perjadin.index')->with('success', 'Perjalanan Dinas berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('perjadin.index')->with('danger', 'Perjalanan Dinas gagal diajukan karena.');
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
        // Ambil data perjalanan dinas beserta relasi individu/tim dan pengikut
        $perjalanan = PerjalananDinas::with([
            'individu',
            'tim' => function ($query) {
                $query->with('pengikut'); // <-- Penting: load relasi pengikut
            }
        ])->findOrFail($id);

        // Ambil semua pegawai dan pejabat untuk dropdown
        $pegawai = Pegawai::all();
        $pejabat = Pejabat::with('pegawai')->get();

        return view('surattugas::dinas_luar.edit', compact('perjalanan', 'pegawai', 'pejabat'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|in:individu,tim',
            'pejabat_id' => 'required|exists:pejabats,id',
            'nomor_surat' => 'required|string|unique:perjalanan_dinas,nomor_surat,' . $id,

            // Validasi untuk individu
            'pegawai_id' => $request->jenis === 'individu' ? 'required|exists:pegawais,id' : 'nullable',
            'kegiatan' => $request->jenis === 'individu' ? 'required|string' : 'nullable',
            'tanggal' => $request->jenis === 'individu' ? 'required|string' : 'nullable',
            'tempat' => $request->jenis === 'individu' ? 'required|string' : 'nullable',

            // Validasi untuk tim
            'pegawai_id_tim' => $request->jenis === 'tim' ? 'required|exists:pegawais,id' : 'nullable',
            'maksud' => $request->jenis === 'tim' ? 'required|string' : 'nullable',
            'alat_angkutan' => $request->jenis === 'tim' ? 'required|string' : 'nullable',
            'lama_perjalanan' => $request->jenis === 'tim' ? 'required|integer|min:1' : 'nullable',
            'tanggal_tim' => $request->jenis === 'tim' ? 'required|string' : 'nullable',
            'pengikut_ids' => $request->jenis === 'tim' ? 'nullable|array' : 'nullable',
        ]);

        $perjalanan = PerjalananDinas::findOrFail($id);
        $dates = explode(' to ', $request->tanggal ?? $request->tanggal_tim);

        $data = [
            'jenis' => $request->jenis,
            'pejabat_id' => $request->pejabat_id,
            'nomor_surat' => $request->nomor_surat,
        ];

        $perjalanan->update($data);

        if ($request->jenis === 'individu') {
            $perjalanan->individu()->updateOrCreate([], [
                'pegawai_id' => $request->pegawai_id,
                'kegiatan' => $request->kegiatan,
                'tanggal_mulai' => $dates[0] ?? null,
                'tanggal_selesai' => $dates[1] ?? $dates[0],
                'tempat' => $request->tempat,
            ]);
        } elseif ($request->jenis === 'tim') {
            $perjalanan->tim()->delete(); // Hapus dulu jika ada

            $tim = $perjalanan->tim()->create([
                'pegawai_id' => $request->pegawai_id_tim,
                'maksud' => $request->maksud,
                'alat_angkutan' => $request->alat_angkutan,
                'lama_perjalanan' => $request->lama_perjalanan,
                'tanggal_berangkat' => $dates[0] ?? null,
                'tanggal_kembali' => $dates[1] ?? $dates[0],
            ]);

            // Update pengikut
            $tim->pengikut()->delete();
            if ($request->has('pengikut_ids')) {
                foreach ($request->pengikut_ids as $pegawaiId) {
                    $tim->pengikut()->create([
                        'pegawai_id' => $pegawaiId,
                    ]);
                }
            }
        }

        return redirect()->route('perjadin.index')->withSuccess('Data berhasil diperbarui.');
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
