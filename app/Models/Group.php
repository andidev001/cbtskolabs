<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function examSchedules()
    {
        return $this->belongsToMany(ExamSchedule::class , 'exam_schedule_groups');
    }
}
