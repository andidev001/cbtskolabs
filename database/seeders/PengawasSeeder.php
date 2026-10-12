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
        \App\Models\Pengawas::firstOrCreate(
            ['username' => 'pengawas'],
            [
                'name' => 'Pengawas Ujian',
                'password' => \Illuminate\Support\Facades\Hash::make('pengawas123'),
            ]
        );
    }
}
