<?php

namespace App\Models\Traits;

// Para sa mga table na created_at lang ang meron at walang updated_at
// (project_users, task_times at task_time_history)
trait CreatedAtOnly
{
    public function getUpdatedAtColumn(): ?string
    {
        return null;
    }
}
