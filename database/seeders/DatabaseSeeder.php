<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // Initial na data ng system. Patakbuhin gamit ang: php artisan migrate:fresh --seed
    public function run(): void
    {
        $companyId = 1; // Main branch. Kapag may sister branch, 2 na ang company_id nila

        // USERS: isang manager at isang user na pang-test
        $managerId = DB::table('users')->insertGetId([
            'company_id' => $companyId,
            'username' => 'ben.cruz',
            'password' => Hash::make('password'),
            'first_name' => 'Ben',
            'last_name' => 'Cruz',
            'system_role' => 'Manager',
            'position' => 'Project Manager',
        ]);

        $userId = DB::table('users')->insertGetId([
            'company_id' => $companyId,
            'username' => 'ana.reyes',
            'password' => Hash::make('password'),
            'first_name' => 'Ana',
            'last_name' => 'Reyes',
            'system_role' => 'User',
            'position' => 'Developer',
        ]);

        // PROJECTS: isang project na hawak ni Ben
        $projectId = DB::table('projects')->insertGetId([
            'company_id' => $companyId,
            'name' => 'Project A',
            'code' => 'PRJA',
            'manager_id' => $managerId,
        ]);

        // PROJECT_USERS: naka-assign si Ana sa Project A
        DB::table('project_users')->insert([
            'company_id' => $companyId,
            'project_id' => $projectId,
            'user_id' => $userId,
            'manager' => false,
            'assigned_by' => $managerId,
        ]);
    }
}