<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pejabat;

class PerjalananDinas extends Model
{
    use HasFactory;

    protected $table = 'perjalanan_dinas';

    protected $fillable = [
        'nomor_surat',
        'pejabat_id',
        'jenis',
    ];

    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class);
    }

    public function individu()
    {
        return $this->hasOne(PerjalananDinasIndividu::class);
    }

    public function tim()
    {
        return $this->hasOne(PerjalananDinasTim::class);
    }

    public function laporan()
    {
        return $this->hasOne(LaporanPerjalananDinas::class);
    }
}
