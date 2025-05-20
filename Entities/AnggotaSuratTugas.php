<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pegawai;

class AnggotaSuratTugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_tugas_id',
        'pegawai_id',
    ];

    // Relasi ke SuratTugas
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class);
    }

    // Relasi ke Pegawai (anggota tim)
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
