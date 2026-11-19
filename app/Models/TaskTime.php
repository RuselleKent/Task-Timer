<?php

namespace App\Models;

use App\Models\Traits\CreatedAtOnly;
use App\Models\Traits\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// TASK_TIMES: Dito lang naka-save ang current time ng task
class TaskTime extends Model
{
    use CreatedAtOnly, HasCreator;

    protected $table = 'task_times';

    protected $fillable = [
        'company_id',
        'task_id',
        'start_time',
        'end_time',
        'end_reason',
        'altered',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'altered' => 'boolean',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    // Mga lumang oras ng record na 'to
    public function history(): HasMany
    {
        return $this->hasMany(TaskTimeHistory::class, 'task_time_id');
    }

    // Tumatakbo pa ba? Null pa kasi ang end_time
    public function isRunning(): bool
    {
        return $this->end_time === null;
    }
}
