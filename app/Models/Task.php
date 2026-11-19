<?php

namespace App\Models;

use App\Models\Traits\HasCreator;
use App\Models\Traits\HasUpdater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// TASKS: Lahat ng task na sinimulan ng user
class Task extends Model
{
    use HasCreator, HasUpdater;

    protected $table = 'tasks';

    protected $fillable = [
        'company_id',
        'user_id',
        'project_id', // Null kapag Non-production
        'title',
        'type',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    // Kung sinong user ang may-ari ng task
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Lahat ng time records ng task (bawat start o resume ay may bagong row)
    public function times(): HasMany
    {
        return $this->hasMany(TaskTime::class, 'task_id');
    }

    // Para sa "Edited" tag sa Tasks page
    public function hasAlteredTime(): bool
    {
        return $this->times()->where('altered', true)->exists();
    }
}
