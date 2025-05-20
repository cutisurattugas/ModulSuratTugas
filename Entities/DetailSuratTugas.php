<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pegawai;

class DetailSuratTugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_tugas_id',
        'pegawai_id',
        'kegiatan_maksud',
        'tanggal_mulai',
        'tanggal_selesai',
        'tempat',
        'alat_angkutan', // nullable (hanya untuk luar kota)
        'lama_perjalanan', // nullable (hanya untuk tim)
    ];

    // Relasi ke SuratTugas
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class);
    }

    // Relasi ke Pegawai (yang ditugaskan)
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Contoh Method: Hitung durasi tugas
    public function getDurasiHariAttribute()
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }

}
