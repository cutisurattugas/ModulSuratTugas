<?php

namespace Modules\SuratTugas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanPerjalananDinas extends Model
{
    use HasFactory;

    protected $table = 'laporan_perjalanan_dinas';

    protected $fillable = [
        'perjalanan_dinas_id',
        'file_laporan',
        'penilaian',
        'tanggal_upload',
    ];

    public function perjalanan()
    {
        return $this->belongsTo(PerjalananDinas::class, 'perjalanan_dinas_id');
    }
}
