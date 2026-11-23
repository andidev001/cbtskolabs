<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'guru_id',
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

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Filter Bank Soal milik guru yang sedang login (atau Bank Soal mapel tersebut yang tidak dikunci ke guru lain).
     */
    public function scopeForGuru($query, $guru)
    {
        $subjectIds = $guru->subjects()->pluck('subjects.id')->toArray();

        return $query->whereIn('subject_id', $subjectIds)
            ->where(function ($q) use ($guru) {
                $q->where('guru_id', $guru->id)
                  ->orWhereNull('guru_id');
            });
    }

    /**
     * Cek apakah Bank Soal ini dapat diakses oleh guru tertentu.
     */
    public function isOwnedByGuru($guru): bool
    {
        $subjectIds = $guru->subjects()->pluck('subjects.id')->toArray();
        if (!in_array($this->subject_id, $subjectIds)) {
            return false;
        }

        return is_null($this->guru_id) || (int) $this->guru_id === (int) $guru->id;
    }
}
