<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guru extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'username',
        'email',
        'phone',
        'photo',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Mata pelajaran yang diampu guru ini */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class , 'guru_subject');
    }

    /** Daftar nama mata pelajaran (helper) */
    public function subjectNames(): string
    {
        return $this->subjects->pluck('name')->join(', ') ?: '-';
    }
}
