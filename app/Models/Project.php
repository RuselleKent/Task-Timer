<?php

namespace App\Models;

use App\Models\Traits\HasCreator;
use App\Models\Traits\HasUpdater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// PROJECTS: Listahan ng mga project
class Project extends Model
{
    use HasCreator, HasUpdater;

    protected $table = 'projects';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'manager_id',
        'created_by',
        'updated_by',
    ];

    // Main manager na may hawak ng project
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Lahat ng assignment rows, pati yung mga naalis na
    public function projectUsers(): HasMany
    {
        return $this->hasMany(ProjectUser::class, 'project_id');
    }

    // Mga active member lang (hindi pa naaalis)
    public function activeMembers(): HasMany
    {
        return $this->projectUsers()->whereNull('removed_at');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }
}
