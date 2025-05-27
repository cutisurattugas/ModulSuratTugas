<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pejabat;

class SuratTugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_token',
        'nomor_surat',
        'status',
        'jenis',
        'jarak',
        'wadir2_id',
        'tanggal_disetujui_wadir2',
        'pimpinan_id',
        'tanggal_disetujui_pimpinan',
    ];

    // Relasi ke Pejabat penandatangan
    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class);
    }

    // Relasi ke DetailSuratTugas (untuk data individu/tim)
    public function detail()
    {
        return $this->hasOne(DetailSuratTugas::class);
    }

    // Relasi ke Anggota (jika jenis = tim)
    public function anggota()
    {
        return $this->hasMany(AnggotaSuratTugas::class);
    }

    // Relasi ke Laporan
    public function laporan()
    {
        return $this->hasOne(LaporanSuratTugas::class);
    }

    // Contoh Accessor: Cek apakah surat luar kota
    public function getIsLuarKotaAttribute()
    {
        return $this->jarak === 'luar_kota';
    }
}
