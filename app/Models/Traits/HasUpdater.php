<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Para lang sa mga table na may updated_by (users, projects, tasks): kung sinong user ang huling nag-edit
trait HasUpdater
{
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
