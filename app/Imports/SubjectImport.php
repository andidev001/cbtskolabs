<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        if (empty($row['nama'])) {
            return null;
        }

        return new Subject([
            'name' => $row['nama'],
            'code' => $row['kode'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required',
        ];
    }
}
