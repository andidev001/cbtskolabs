<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Siswa extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nomer_ujian',
        'name',
        'username',
        'nisn',
        'religion_id',
        'jurusan_id',
        'group_id',
        'sesi',
        'status',
        'password',
        'password_raw',
        'is_logged_in',
        'session_id',
    ];

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
