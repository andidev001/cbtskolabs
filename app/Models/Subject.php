<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'religion_id'];

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    /** Guru-guru yang mengampu mata pelajaran ini */
    public function gurus()
    {
        return $this->belongsToMany(Guru::class , 'guru_subject');
    }
}
