<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Group;
use App\Models\Jurusan;
use App\Models\Religion;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Find or skip related data
        $group = Group::where('name', $row['grup'])->first();
        $jurusan = Jurusan::where('name', $row['jurusan'])->first() ?? Jurusan::where('code', $row['jurusan'])->first();
        $religion = Religion::where('name', $row['agama'])->first();

        return new Siswa([
            'nomer_ujian' => $row['nomer_ujian'],
            'name' => $row['nama'],
            'username' => $row['username'],
            'password' => Hash::make($row['password']),
            'password_raw' => $row['password'],
            'religion_id' => $religion ? $religion->id : null,
            'jurusan_id' => $jurusan ? $jurusan->id : null,
            'group_id' => $group ? $group->id : null,
            'sesi' => $row['sesi'] ?? '1',
            'status' => true,
        ]);
    }
}
