<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Guru::firstOrCreate(
            ['username' => 'guru'],
            [
                'name' => 'Guru Penguji',
                'email' => 'guru@SKOLABScbt.com',
                'password' => \Illuminate\Support\Facades\Hash::make('guru123'),
            ]
        );
    }
}
