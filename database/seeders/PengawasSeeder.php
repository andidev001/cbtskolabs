<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengawasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Pengawas::create([
            'name' => 'Pengawas Ujian',
            'username' => 'pengawas',
            'password' => bcrypt('password')
        ]);
    }
}
