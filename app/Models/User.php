<?php

namespace App\Models;

use App\Models\Traits\HasCreator;
use App\Models\Traits\HasUpdater;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

// USERS: Lahat ng may access sa system (User at Manager)
class User extends Authenticatable
{
    use HasCreator, HasUpdater;

    protected $table = 'users';

    // Walang remember_token column ang users table natin, kaya nilagay ko 'tong empty para hindi mag-error sa logout
    protected $rememberTokenName = '';

    protected $fillable = [
        'company_id',
        'username',
        'password',
        'authentication',
        'authentication_key',
        'status',
        'first_name',
        'last_name',
        'system_role',
        'position',
        'created_by',
        'updated_by',
    ];

    // Hindi dapat lumabas ang mga ito kapag ginawang array o JSON ang user
    protected $hidden = [
        'password',
        'authentication_key',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed', // Automatic na ina-hash kapag sine-save
        ];
    }

    // Full name na ipapakita sa interface (first name + last name)
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isManager(): bool
    {
        return $this->system_role === 'Manager';
    }

    // Mga project na hawak niya bilang manager
    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    // Mga project kung saan naka-assign siya
    public function projectUsers(): HasMany
    {
        return $this->hasMany(ProjectUser::class, 'user_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'user_id');
    }
}
