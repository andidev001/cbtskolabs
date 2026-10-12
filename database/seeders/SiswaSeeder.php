<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Siswa::firstOrCreate(
            ['username' => 'siswa'],
            [
                'name' => 'Siswa Percobaan',
                'nomer_ujian' => 'UJI-001',
                'nisn' => '1234567890',
                'password' => \Illuminate\Support\Facades\Hash::make('siswa123'),
                'password_raw' => 'siswa123',
                'status' => 1,
            ]
        );
    }
}
