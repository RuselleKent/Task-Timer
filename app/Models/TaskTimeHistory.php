<?php

namespace App\Models;

use App\Models\Traits\CreatedAtOnly;
use App\Models\Traits\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// TASK_TIME_HISTORY: Mga lumang oras lang na napalitan. Hindi na ito ine-edit o binubura
class TaskTimeHistory extends Model
{
    use CreatedAtOnly, HasCreator;

    // Kailangan i-set kasi "task_time_histories" ang hahulaan ng Laravel na pangalan
    protected $table = 'task_time_history';

    protected $fillable = [
        'company_id',
        'task_time_id',
        'old_start_time',
        'old_end_time',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'old_start_time' => 'datetime',
            'old_end_time' => 'datetime',
        ];
    }

    // Bawal i-edit at i-delete ang history, kahit sa code
    protected static function booted(): void
    {
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    public function taskTime(): BelongsTo
    {
        return $this->belongsTo(TaskTime::class, 'task_time_id');
    }

    // Kung sinong user ang nagbago ng oras
}
