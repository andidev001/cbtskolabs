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
        \App\Models\Siswa::create([
            'name' => 'Siswa Percobaan',
            'username' => 'siswa',
            'nisn' => '1234567890',
            'password' => \Illuminate\Support\Facades\Hash::make('siswa123'),
        ]);
    }
}
