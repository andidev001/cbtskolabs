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
        // Skip empty rows (Excel often includes trailing empty rows)
        $nama = !empty($row['nama']) ? trim((string)$row['nama']) : null;
        $username = !empty($row['username']) ? trim((string)$row['username']) : null;
        $nomerUjian = !empty($row['nomer_ujian']) 
            ? trim((string)$row['nomer_ujian']) 
            : (!empty($row['nomor_ujian']) 
                ? trim((string)$row['nomor_ujian']) 
                : (!empty($row['no_peserta']) 
                    ? trim((string)$row['no_peserta']) 
                    : (!empty($row['no_ujian']) 
                        ? trim((string)$row['no_ujian']) 
                        : null)));

        // If row is completely empty or missing both name and username, skip it
        if (empty($nama) && empty($username) && empty($nomerUjian)) {
            return null;
        }

        // Fallbacks if one is missing
        $username = $username ?: ($nomerUjian ?: 'peserta_' . uniqid());
        $nomerUjian = $nomerUjian ?: $username;
        $nama = $nama ?: $username;

        $passwordRaw = !empty($row['password']) ? (string)$row['password'] : '123456';
        $sesi = !empty($row['sesi']) ? (string)$row['sesi'] : '1';

        // Find or skip related data
        $grupName = !empty($row['grup']) ? trim((string)$row['grup']) : null;
        $group = $grupName ? Group::where('name', $grupName)->first() : null;

        $jurusanName = !empty($row['jurusan']) ? trim((string)$row['jurusan']) : null;
        $jurusan = $jurusanName 
            ? (Jurusan::where('name', $jurusanName)->first() ?? Jurusan::where('code', $jurusanName)->first()) 
            : null;

        $agamaName = !empty($row['agama']) ? trim((string)$row['agama']) : null;
        $religion = $agamaName ? Religion::where('name', $agamaName)->first() : null;

        return Siswa::updateOrCreate(
            ['username' => $username],
            [
                'nomer_ujian' => $nomerUjian,
                'name' => $nama,
                'password' => Hash::make($passwordRaw),
                'password_raw' => $passwordRaw,
                'religion_id' => $religion ? $religion->id : null,
                'jurusan_id' => $jurusan ? $jurusan->id : null,
                'group_id' => $group ? $group->id : null,
                'sesi' => $sesi,
                'status' => true,
            ]
        );
    }
}
