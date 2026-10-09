<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        // Return an empty array since we only want the template headers
        return [];
    }

    public function headings(): array
    {
        return [
            'nomer_ujian', 
            'nama', 
            'username', 
            'password', 
            'grup', 
            'jurusan', 
            'agama', 
            'sesi'
        ];
    }
}
