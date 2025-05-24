<?php
namespace Modules\SuratTugas\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuratTugasExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($surat) {
            return [
                $surat->nomor_surat,
                optional(optional($surat->detail)->pegawai)->nama ?? '-',
                ucfirst($surat->jenis),
                optional($surat->detail)->kegiatan_maksud ?? '-',
                optional($surat->detail)->tanggal_mulai . ' - ' . optional($surat->detail)->tanggal_selesai,
                optional($surat->detail)->kota_tujuan ?? '-',
                optional($surat->detail)->alat_angkutan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nomor Surat',
            'Nama Pegawai',
            'Jenis',
            'Kegiatan',
            'Tanggal',
            'Kota Tujuan',
            'Alat Angkutan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}