<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pengaturan\Entities\Pegawai;
use Modules\SuratTugas\Entities\PerjalanDinas;

class PerjalananDinasIndividu extends Model
{
    use HasFactory;
    protected $table = 'perjalanan_dinas_individu';

    protected $fillable = [
        'perjalanan_dinas_id',
        'pegawai_id',
        'kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tempat',
    ];

    public function perjalanan()
    {
        return $this->belongsTo(PerjalananDinas::class, 'perjalanan_dinas_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
