<?php

namespace Modules\SuratTugas\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        // Query dasar dengan relasi
        $query = SuratTugas::with([
            'pejabat',
            'detail',
            'detail.pegawai',
            'anggota.pegawai',
            'laporan'
        ]);

        // Inisialisasi variabel
        $surat_tugas = collect(); // Default kosong

        if ($role === 'admin') {
            // Admin: lihat semua surat tugas
            $surat_tugas = $query->latest()->get();
        } elseif ($role === 'direktur') {
            // Direktur: lihat semua surat tugas (tanpa filter sama sekali)
            $surat_tugas = $query->latest()->get();
        } elseif ($role === 'wadir2') {
            // Wadir2:
            // - Bisa lihat semua surat seperti direktur
            // - DAN tambahan surat yang dia terlibat (individu/tim)

            // Ambil semua surat (seperti direktur)
            $semua_surat = $query->latest()->get();

            // Ambil surat yang terlibat sebagai pelaksana (individu/tim)
            $milik_sendiri = SuratTugas::with([
                'pejabat',
                'detail',
                'detail.pegawai',
                'anggota.pegawai',
                'laporan'
            ])->where(function ($q) use ($pegawai_id) {
                // Individu
                $q->where('jenis', 'individu')
                    ->whereHas('detail', function ($q2) use ($pegawai_id) {
                        $q2->where('pegawai_id', $pegawai_id);
                    });

                // Atau Tim
                $q->orWhere(function ($q3) use ($pegawai_id) {
                    $q3->where('jenis', 'tim')
                        ->whereHas('detail', function ($q4) use ($pegawai_id) {
                            $q4->where('pegawai_id', $pegawai_id);
                        });
                });
            })->get();

            // Gabungkan dan hilangkan duplikasi
            $surat_tugas = $semua_surat->merge($milik_sendiri)->unique('id');
        } elseif (in_array($role, ['pegawai', 'dosen', 'staf'])) {
            // Pegawai biasa hanya lihat surat tugas individu & tim yang dia terlibat
            $surat_tugas = $query->where(function ($q) use ($pegawai_id) {
                // Surat individu dimana dia pelaksana
                $q->where('jenis', 'individu')
                    ->whereHas('detail', function ($q2) use ($pegawai_id) {
                        $q2->where('pegawai_id', $pegawai_id);
                    });

                // ATAU Surat tim dimana dia sebagai ketua (bukan sekadar anggota)
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

        // Tambahkan logika tambahan jika role adalah wadir2
        if ($role === 'wadir2') {
            // Ambil hanya surat tugas individu & tim yang wadir2 jadi pelaksana
            $dinas_individu = $dinas_individu->filter(function ($surat) use ($pegawai_id) {
                return $surat->detail->pegawai_id == $pegawai_id;
            });

            $dinas_tim = $dinas_tim->filter(function ($surat) use ($pegawai_id) {
                return $surat->detail->pegawai_id == $pegawai_id;
            });
        }

        $dinas_semua = $surat_tugas;

        // Tentukan $dinas_data berdasarkan mode
        $mode = request()->query('mode', 'semua'); // default: 'semua'

        switch ($mode) {
            case 'individu':
                $dinas_data = $dinas_individu;
                break;
            case 'kelompok':
                $dinas_data = $dinas_tim;
                break;
            case 'semua':
            default:
                $dinas_data = $dinas_semua;
                break;
        }

        return view('surattugas::surattugas.index', compact(
            'dinas_individu',
            'dinas_tim',
            'dinas_semua',
            'dinas_data',
            'mode'
        ));
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
            'jenis' => 'required|in:individu,tim',
            'jarak' => 'required|in:dalam_kota,luar_kota', // Added distance field
        ]);

        $getDirektur = Pejabat::where('jabatan_id', 1)->first();
        $getWadir2 = Pejabat::where('jabatan_id', 3)->first();

        DB::beginTransaction();

        try {
            // Buat surat tugas utama
            $suratTugas = SuratTugas::create([
                'nomor_surat' => $request->nomor_surat,
                'jenis' => $request->jenis,
                'jarak' => $request->jarak,
                'access_token' => Str::uuid(),
                'wadir2_id' => optional($getDirektur)->id,
                'pimpinan_id' => optional($getDirektur)->id,
                'status' => 'diproses',
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
     * @param int $access_token
     * @return Renderable
     */
    public function show($access_token)
    {
        $surat = SuratTugas::where('access_token', $access_token)->firstOrFail();

        return view('surattugas::surattugas.show', compact('surat'));
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

    public function approvedBy(Request $request, $access_token)
    {
        $user = auth()->user();
        $suratTugas = SuratTugas::where('access_token', $access_token)->first();

        if (!$suratTugas) {
            return redirect()->route('surattugas.index')->with('danger', 'Surat tugas tidak ditemukan.');
        }

        DB::beginTransaction();

        try {
            $pegawai = Pegawai::where('username', $user->username)->first();

            if (!$pegawai) {
                throw new \Exception('Data pegawai tidak ditemukan.');
            }

            // Cari ID pejabat berdasarkan pegawai_id
            $pejabat = Pejabat::where('pegawai_id', $pegawai->id)->first();

            if (!$pejabat) {
                throw new \Exception('Data pejabat tidak ditemukan untuk pegawai ini.');
            }

            if ($user->role_aktif === 'wadir2') {
                // Approval oleh Wadir 2
                $suratTugas->wadir2_id = $pejabat->id;
                $suratTugas->tanggal_disetujui_wadir2 = now();
                $suratTugas->status = 'diproses'; // Masih menunggu persetujuan pimpinan
                $message = 'Disetujui oleh Wadir 2.';
            } elseif ($user->role_aktif === 'direktur') {
                // Direktur hanya bisa menyetujui jika sudah disetujui oleh Wadir 2
                if (!$suratTugas->tanggal_disetujui_wadir2) {
                    return redirect()->route('surattugas.index')->with('danger', 'Surat tugas belum disetujui oleh Wadir 2.');
                }

                // Approval oleh Direktur
                $suratTugas->pimpinan_id = $pejabat->id;
                $suratTugas->tanggal_disetujui_pimpinan = now();
                $suratTugas->status = 'disetujui';
                $message = 'Disetujui oleh Direktur.';
            } else {
                return redirect()->route('surattugas.index')->with('danger', 'Anda tidak memiliki hak untuk menyetujui surat tugas.');
            }

            $suratTugas->save();

            DB::commit();

            return redirect()->route('surattugas.index')->with('success', 'Surat tugas berhasil disetujui. ' . $message);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('surattugas.index')->with('danger', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function upload(Request $request, $access_token)
    {
        // Validasi file laporan
        $request->validate([
            'file_laporan' => 'required|mimes:pdf,docx|max:10240' // tambahkan docx jika perlu
        ]);

        DB::beginTransaction();

        try {
            $suratTugas = SuratTugas::where('access_token', $access_token)->firstOrFail();

            // Update status jika perlu
            $suratTugas->status = 'diproses';
            $suratTugas->save();

            // Cek apakah laporan sebelumnya sudah ada
            $existingLaporan = $suratTugas->laporan;

            // Hapus file lama jika ada
            if ($existingLaporan && Storage::disk('public')->exists($existingLaporan->file_laporan)) {
                Storage::disk('public')->delete($existingLaporan->file_laporan);
            }

            // Simpan file baru
            $path = $request->file('file_laporan')->store('laporan', 'public');

            // Update atau buat laporan baru
            LaporanSuratTugas::updateOrCreate(
                ['surat_tugas_id' => $suratTugas->id],
                [
                    'file_laporan' => $path,
                    'tanggal_upload' => now()
                ]
            );

            DB::commit();

            return back()->with('success', 'Laporan berhasil diunggah (diperbarui)!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('danger', 'Terjadi kesalahan saat mengunggah laporan: ' . $e->getMessage());
        }
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
        $qrCodeUrl = url("/scan-surattugas/" . $suratTugas->access_token);
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

    public function scanSuratTugas($access_token)
    {
        $data_direktur = Pejabat::where('jabatan_id', '1')->first();

        // Get surat tugas with all necessary relationships
        $suratTugas = SuratTugas::with([
            'pejabat.pegawai',
            'detail.pegawai',
            'anggota.pegawai',
            'laporan'
        ])->where('access_token', $access_token)->firstOrFail();

        return view('surattugas::surattugas.scan',  [
            'perjalanan' => $suratTugas,
            'detail' => $suratTugas->detail,
            'direktur' => $data_direktur
        ]);
    }
}
