<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_event_id',
        'bank_soal_id',
        'name',
        'start_at',
        'end_at',
        'duration',
        'is_active',
        'token',
        'token_interval',
        'token_refreshed_at',
        'show_score',
        'show_percentage',
        'show_answer',
        'enforce_focus'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'token_refreshed_at' => 'datetime',
        'show_score' => 'boolean',
        'show_percentage' => 'boolean',
        'show_answer' => 'boolean',
        'enforce_focus' => 'boolean',
    ];

    /**
     * Refresh token if interval is met
     */
    public function maybeRefreshToken()
    {
        if (!$this->token || !$this->token_interval) {
            return false;
        }

        $lastRefreshed = $this->token_refreshed_at ?? $this->updated_at;
        $diffMinutes = now()->diffInMinutes($lastRefreshed);

        if ($diffMinutes >= $this->token_interval) {
            $newToken = strtoupper(\Illuminate\Support\Str::random(6));
            $this->update([
                'token' => $newToken,
                'token_refreshed_at' => now()
            ]);
            return $newToken;
        }

        return false;
    }

    public function examEvent()
    {
        return $this->belongsTo(ExamEvent::class);
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class , 'exam_schedule_groups');
    }
}
