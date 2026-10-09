<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'code',
        'name',
        'pg_count', 'pg_percent',
        'pgk_count', 'pgk_percent',
        'ls_count', 'ls_percent',
        'mj_count', 'mj_percent',
        'is_count', 'is_percent',
        'ur_count', 'ur_percent',
        'mt_count', 'mt_percent',
        'bs_count', 'bs_percent',
        'st_count', 'st_percent',
        'status'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
