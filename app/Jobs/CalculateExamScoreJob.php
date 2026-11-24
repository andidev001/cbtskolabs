<?php

namespace App\Jobs;

use App\Models\ExamResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateExamScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scheduleId;
    public $siswaId;

    /**
     * Create a new job instance.
     */
    public function __construct($scheduleId, $siswaId)
    {
        $this->scheduleId = $scheduleId;
        $this->siswaId = $siswaId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ExamResult::recalculateExisting($this->scheduleId, $this->siswaId);
    }
}
