<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_soal_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'answer',
        'type',
        'cognitive_level',
        'audio_file',
        'audio_path',
        'complex_options',
        'layout'
    ];

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }
}
