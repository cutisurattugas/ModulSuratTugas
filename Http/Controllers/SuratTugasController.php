<?php

namespace Modules\SuratTugas\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Pengaturan\Entities\Pegawai;
use Modules\Pengaturan\Entities\Pejabat;
use Modules\SuratTugas\Entities\AnggotaSuratTugas;
use Modules\SuratTugas\Entities\DetailSuratTugas;
use Modules\SuratTugas\Entities\SuratTugas;
use Illuminate\Support\Str;
use Modules\SuratTugas\Entities\LaporanSuratTugas;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratTugasController extends Controller
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

        // Query dasar dengan relasi
        $query = SuratTugas::with([
            'pejabat',
            'detail',
            'detail.pegawai',
            'anggota.pegawai',
            'laporan'
        ]);

        // Filter berdasarkan role
        if ($role === 'admin') {
            // Admin bisa lihat semua data
            $surat_tugas = $query->latest()->get();
        } elseif (in_array($role, ['direktur', 'wadir1', 'wadir2', 'wadir3'])) {
            // Pejabat hanya lihat surat yang mereka tanda tangani
            $surat_tugas = $query->where('pejabat_id', $pejabat_id)
                ->latest()
                ->get();
        } elseif (in_array($role, ['pegawai', 'dosen', 'staf'])) {
            // Pegawai biasa hanya melihat:
            // 1. Surat tugas individu dimana dia sebagai pelaksana
            // 2. Surat tugas tim dimana dia sebagai KETUA TIM (bukan anggota)

            $surat_tugas = $query->where(function ($q) use ($pegawai_id) {
                // Surat tugas individu (dia sebagai pelaksana)
                $q->where('jenis', 'individu')
                    ->whereHas('detail', function ($q2) use ($pegawai_id) {
                        $q2->where('pegawai_id', $pegawai_id);
                    });

                // ATAU Surat tugas tim (dia sebagai ketua tim)
                $q->orWhere(function ($q3) use ($pegawai_id) {
                    $q3->where('jenis', 'tim')
                        ->whereHas('detail', function ($q4) use ($pegawai_id) {
                            $q4->where('pegawai_id', $pegawai_id);
                        });
                });
            })
                ->latest()
                ->get();
        }

        // Pisahkan data untuk view
        $dinas_individu = $surat_tugas->where('jenis', 'individu');
        $dinas_tim = $surat_tugas->where('jenis', 'tim');

        return view('surattugas::surattugas.index', compact('dinas_individu', 'dinas_tim'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $pejabat = Pejabat::with('pegawai')->get();
        $pegawai = Pegawai::all();
        return view('surattugas::surattugas.create', compact('pejabat', 'pegawai'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|unique:surat_tugas,nomor_surat',
            'pejabat_id' => 'required|exists:pejabats,id',
            'jenis' => 'required|in:individu,tim',
            'jarak' => 'required|in:dalam_kota,luar_kota', // Added distance field
        ]);

        DB::beginTransaction();

        try {
            // Buat surat tugas utama
            $suratTugas = SuratTugas::create([
                'nomor_surat' => $request->nomor_surat,
                'pejabat_id' => $request->pejabat_id,
                'jenis' => $request->jenis,
                'jarak' => $request->jarak,
                'access_token' => substr(Str::uuid(), 0, 12),
            ]);

            // Handle range tanggal
            $tanggalRange = explode(' to ', $request->tanggal);
            $tanggalMulai = $tanggalRange[0];
            $tanggalSelesai = $tanggalRange[1] ?? $tanggalRange[0];

            // Data detail surat tugas
            $detailData = [
                'surat_tugas_id' => $suratTugas->id,
                'pegawai_id' => $request->pegawai_id,
                'kegiatan_maksud' => $request->kegiatan_maksud,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'tempat' => $request->tempat,
            ];

            // Tambahkan alat_angkutan dan kota jika jarak = luar_kota
            if ($request->jarak === 'luar_kota') {
                $request->validate([
                    'alat_angkutan' => 'required|string',
                    'kota_keberangkatan' => 'required|string',
                    'kota_tujuan' => 'required|string',
                ]);

                $detailData['alat_angkutan'] = $request->alat_angkutan;
                $detailData['kota_keberangkatan'] = $request->kota_keberangkatan;
                $detailData['kota_tujuan'] = $request->kota_tujuan;
            }

            // Jika jenis tim, validasi lama_perjalanan
            if ($request->jenis === 'tim') {
                $request->validate([
                    'lama_perjalanan' => 'required|integer|min:1'
                ]);
                $detailData['lama_perjalanan'] = $request->lama_perjalanan;
            }

            // Simpan ke detail_surat_tugas
            $detail = DetailSuratTugas::create($detailData);

            // Jika jenis tim, simpan anggota tambahan
            if ($request->jenis === 'tim' && $request->has('anggota_ids')) {
                foreach ($request->anggota_ids as $anggotaId) {
                    AnggotaSuratTugas::create([
                        'surat_tugas_id' => $suratTugas->id,
                        'pegawai_id' => $anggotaId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('surattugas.index')->with('success', 'Surat Tugas berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('danger', 'Gagal membuat Surat Tugas: ' . $e->getMessage());
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
    public function edit($access_token)
    {
        // Get the surat tugas with all necessary relationships
        $suratTugas = SuratTugas::with([
            'pejabat',
            'detail', // Main details (for both individu and tim)
            'anggota' => function ($query) {
                $query->with('pegawai'); // Load pegawai data for anggota
            },
            'laporan' // Include laporan if needed
        ])->where('access_token', $access_token)
            ->firstOrFail();

        // Get all pegawai and pejabat for dropdowns
        $pegawai = Pegawai::all();
        $pejabat = Pejabat::with('pegawai')->get();

        return view('surattugas::surattugas.edit', [
            'suratTugas' => $suratTugas,
            'pegawai' => $pegawai,
            'pejabat' => $pejabat
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $access_token)
    {
        // Cari surat tugas berdasarkan access_token
        $suratTugas = SuratTugas::where('access_token', $access_token)->firstOrFail();

        // Validasi dasar
        $validationRules = [
            'jenis' => 'required|in:individu,tim',
            'pejabat_id' => 'required|exists:pejabats,id',
            'nomor_surat' => 'required|string|unique:surat_tugas,nomor_surat,' . $suratTugas->id,
            'jarak' => 'required|in:dalam_kota,luar_kota',
            'pegawai_id' => 'required|exists:pegawais,id',
            'kegiatan_maksud' => 'required|string',
            'tempat' => 'required|string',
            'tanggal' => 'required|string',
            'lama_perjalanan' => 'nullable|integer|min:1',
            'anggota_ids' => 'nullable|array',
            'anggota_ids.*' => 'exists:pegawais,id',
        ];

        // Tambahkan validasi tambahan jika jarak == luar_kota
        if ($request->jarak === 'luar_kota') {
            $validationRules['alat_angkutan'] = 'required|string';
            $validationRules['kota_keberangkatan'] = 'required|string';
            $validationRules['kota_tujuan'] = 'required|string';
        }

        $request->validate($validationRules);

        DB::beginTransaction();

        try {
            // Pisahkan tanggal
            $dates = explode(' to ', $request->tanggal);
            $tanggalMulai = $dates[0] ?? null;
            $tanggalSelesai = $dates[1] ?? $dates[0];

            // Update surat tugas utama
            $suratTugas->update([
                'nomor_surat' => $request->nomor_surat,
                'pejabat_id' => $request->pejabat_id,
                'jenis' => $request->jenis,
                'jarak' => $request->jarak,
            ]);

            // Siapkan data detail
            $detailData = [
                'pegawai_id' => $request->pegawai_id,
                'kegiatan_maksud' => $request->kegiatan_maksud,
                'tempat' => $request->tempat,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ];

            // Tambahkan field hanya jika jarak = luar_kota
            if ($request->jarak === 'luar_kota') {
                $detailData['alat_angkutan'] = $request->alat_angkutan;
                $detailData['kota_keberangkatan'] = $request->kota_keberangkatan;
                $detailData['kota_tujuan'] = $request->kota_tujuan;
            }

            // Tambahkan lama_perjalanan hanya jika jenis = tim
            if ($request->jenis === 'tim') {
                $detailData['lama_perjalanan'] = $request->lama_perjalanan;
            }

            // Update atau buat detail surat tugas
            $suratTugas->detail()->updateOrCreate([], $detailData);

            // Handle anggota jika jenis = tim
            if ($request->jenis === 'tim') {
                $currentAnggotaIds = $suratTugas->anggota->pluck('pegawai_id')->toArray();
                $newAnggotaIds = $request->anggota_ids ?? [];

                // Hapus anggota yang tidak ada di input baru
                $toRemove = array_diff($currentAnggotaIds, $newAnggotaIds);
                if (!empty($toRemove)) {
                    $suratTugas->anggota()->whereIn('pegawai_id', $toRemove)->delete();
                }

                // Tambahkan anggota baru
                $toAdd = array_diff($newAnggotaIds, $currentAnggotaIds);
                foreach ($toAdd as $pegawaiId) {
                    $suratTugas->anggota()->create(['pegawai_id' => $pegawaiId]);
                }
            } else {
                // Jika berubah dari tim ke individu, hapus semua anggota
                if ($suratTugas->jenis === 'tim') {
                    $suratTugas->anggota()->delete();
                }
            }

            DB::commit();

            return redirect()->route('surattugas.index')->with('success', 'Surat tugas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('danger', 'Gagal memperbarui surat tugas: ' . $e->getMessage());
        }
    }

    public function upload(Request $request, $access_token)
    {
        $request->validate(['file_laporan' => 'required|mimes:pdf|max:10240']);

        $suratTugas = SuratTugas::where('access_token', $access_token)->firstOrFail();
        $path = $request->file('file_laporan')->store('laporan', 'public');

        LaporanSuratTugas::updateOrCreate(
            ['surat_tugas_id' => $suratTugas->id],
            ['file_laporan' => $path, 'tanggal_upload' => now()]
        );

        return back()->with('success', 'Laporan berhasil diunggah!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function printSuratTugas($access_token)
    {
        $data_direktur = Pejabat::where('jabatan_id', '1')->first();

        // Get surat tugas with all necessary relationships
        $suratTugas = SuratTugas::with([
            'pejabat.pegawai',
            'detail.pegawai',
            'anggota.pegawai',
            'laporan'
        ])->where('access_token', $access_token)->firstOrFail();

        // Generate QR Code
        $qrCodeUrl = url("https://prismfox.my.id");
        $qrCodeImage = QrCode::format('svg')->size(100)->generate($qrCodeUrl);

        // Pilih view berdasarkan JARAK, bukan jenis
        $viewName = $suratTugas->jarak === 'dalam_kota'
            ? 'surattugas::pdf.dalam_kota'
            : 'surattugas::pdf.luar_kota';

        return view($viewName, [
            'perjalanan' => $suratTugas,
            'qrCodeImage' => $qrCodeImage,
            'detail' => $suratTugas->detail,
            'direktur' => $data_direktur
        ]);
    }
}
