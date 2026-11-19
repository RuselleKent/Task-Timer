<?php

namespace App\Models;

use App\Models\Traits\CreatedAtOnly;
use App\Models\Traits\HasCreator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// PROJECT_USERS: Kung sinong mga user ang kasama sa isang project
class ProjectUser extends Model
{
    use CreatedAtOnly, HasCreator;

    protected $table = 'project_users';

    protected $fillable = [
        'company_id',
        'project_id',
        'user_id',
        'manager',
        'removed_by',
        'removed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'manager' => 'boolean',
            'removed_at' => 'datetime',
        ];
    }

    // Mga member na hindi pa naaalis
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('removed_at');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Manager na nag-assign sa kaniya

    // Manager na nag-alis sa kaniya
    public function removedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'removed_by');
    }
}
