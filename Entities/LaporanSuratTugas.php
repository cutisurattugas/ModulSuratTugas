<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pegawai;

class LaporanSuratTugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_tugas_id',
        'file_laporan',
        'predikat_penilaian',
        'deskripsi_penilaian',
        'tanggal_upload'
    ];

    // Relasi ke SuratTugas
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class);
    }

    // Relasi ke Pegawai (uploader)
    public function uploader()
    {
        return $this->belongsTo(Pegawai::class, 'uploader_id');
    }
}
