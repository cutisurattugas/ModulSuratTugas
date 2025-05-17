<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pegawai;

class PerjalananDinasTim extends Model
{
    use HasFactory;
    protected $table = 'perjalanan_dinas_tim';

    protected $fillable = [
        'perjalanan_dinas_id',
        'pegawai_id',
        'maksud',
        'alat_angkutan',
        'lama_perjalanan',
        'tanggal_berangkat',
        'tanggal_kembali',
    ];

    public function perjalanan()
    {
        return $this->belongsTo(PerjalananDinas::class, 'perjalanan_dinas_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pengikut()
    {
        return $this->hasMany(PengikutPerjalananDinas::class);
    }
}
