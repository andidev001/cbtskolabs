<?php

namespace App\Imports;

use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        if (empty($row['nama'])) {
            return null;
        }

        return new Guru([
            'name'      => $row['nama'],
            'nip'       => $row['nip'] ?? null,
            'username'  => $row['username'],
            'email'     => !empty($row['email']) ? $row['email'] : null,
            'phone'     => $row['telepon'] ?? null,
            'password'  => Hash::make($row['password']),
            'is_active' => 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama'     => 'required',
            'username' => 'required|unique:gurus,username',
            'password' => 'required',
        ];
    }
}
