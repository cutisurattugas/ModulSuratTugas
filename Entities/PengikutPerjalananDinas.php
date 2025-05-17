<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pegawai;

class PengikutPerjalananDinas extends Model
{
    protected $table = 'pengikut_perjalanan_dinas';

    protected $fillable = [
        'perjalanan_dinas_tim_id',
        'pegawai_id',
    ];

    public function tim()
    {
        return $this->belongsTo(PerjalananDinasTim::class, 'perjalanan_dinas_tim_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
