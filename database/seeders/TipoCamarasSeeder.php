<?php

namespace Database\Seeders;

use App\Models\TipoCamaras;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoCamarasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoCamaras::create(['tipo' => 'Domo']);
        TipoCamaras::create(['tipo' => 'Bullet']);
        TipoCamaras::create(['tipo' => 'LPR']);

    }
}
