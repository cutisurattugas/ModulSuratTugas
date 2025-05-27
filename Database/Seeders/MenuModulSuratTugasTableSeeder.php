<?php

namespace Modules\SuratTugas\Database\Seeders;

use App\Models\Core\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class MenuModulSuratTugasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Menu::where('modul', 'Surat Tugas')->delete();
        $menu = Menu::create([
            'modul' => 'SuratTugas',
            'label' => 'Surat Tugas',
            'url' => 'surattugas',
            'can' => serialize(['admin', 'pegawai', 'dosen', 'pimpinan', 'direktur', 'keuangan', 'wadir2']),
            'icon' => 'fas fa-envelope',
            'urut' => 1,
            'parent_id' => 0,
            'active' => serialize(['surattugas']),
        ]);
        if ($menu) {
            Menu::create([
                'modul' => 'SuratTugas',
                'label' => 'Perjalanan Dinas',
                'url' => 'surattugas/perjadin',
                'can' => serialize(['admin', 'pegawai', 'dosen', 'pimpinan', 'direktur', 'keuangan', 'wadir2']),
                'icon' => 'far fa-circle',
                'urut' => 2,
                'parent_id' => $menu->id,
                'active' => serialize(['surattugas/perjadin', 'surattugas/perjadin*']),
            ]);
        }
        if ($menu) {
            Menu::create([
                'modul' => 'SuratTugas',
                'label' => 'Kepanitiaan',
                'url' => 'surattugas/kepanitiaan',
                'can' => serialize(['admin', 'pegawai', 'dosen', 'pimpinan', 'direktur', 'keuangan']),
                'icon' => 'far fa-circle',
                'urut' => 3,
                'parent_id' => $menu->id,
                'active' => serialize(['surattugas/kepanitiaan', 'surattugas/kepanitiaan*']),
            ]);
        }
        if ($menu) {
            Menu::create([
                'modul' => 'SuratTugas',
                'label' => 'Rekap Perjalanan Dinas',
                'url' => 'surattugas/rekap-perjadin',
                'can' => serialize(['admin']),
                'icon' => 'far fa-circle',
                'urut' => 4,
                'parent_id' => $menu->id,
                'active' => serialize(['surattugas/rekap-perjadin', 'surattugas/rekap-perjadin*']),
            ]);
        }
        if ($menu) {
            Menu::create([
                'modul' => 'SuratTugas',
                'label' => 'Rekap Kepanitiaan',
                'url' => 'surattugas/rekap-kepanitiaan',
                'can' => serialize(['admin']),
                'icon' => 'far fa-circle',
                'urut' => 5,
                'parent_id' => $menu->id,
                'active' => serialize(['surattugas/rekap-kepanitiaan', 'surattugas/rekap-kepanitiaan*']),
            ]);
        }
    }
}
