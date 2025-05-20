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
            'can' => serialize(['admin']),
            'icon' => 'fas fa-envelope',
            'urut' => 1,
            'parent_id' => 0,
            'active' => serialize(['surattugas']),
        ]);
        if ($menu) {
            Menu::create([
                'modul' => 'SuratTugas',
                'label' => 'Kelola',
                'url' => 'surattugas/kelola',
                'can' => serialize(['admin']),
                'icon' => 'far fa-circle',
                'urut' => 2,
                'parent_id' => $menu->id,
                'active' => serialize(['surattugas/kelola', 'surattugas/kelola*']),
            ]);
        }
    }
}
