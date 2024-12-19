<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Panel;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userCount = User::count();
        if ($userCount > 0) {
            $this->command->info("$userCount user(s) already exist. Bailing out...");
            return;
        }

        //$userRole = Role::firstOrCreate(['name' => 'user']);
        // Assign user to the team
        $admin = User::create([
            'name' => env('SUPER_ADMIN_NAME', 'admin'),
            'email' => env('SUPER_ADMIN_EMAIL', 'admin@admin.com'),
            'password' => env('SUPER_ADMIN_PASSWORD', '123456'),
        ]);

        // Make the user a super admin
        $admin->assignRole(Utils::getSuperAdminName());
        //system User
        $user = User::create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => 'user',
        ]);
        //Assign panel_user role
        $user->assignRole(Utils::getPanelUserRoleName());

        //Create additional users
        //User::factory()->count(10)->create(); // Creates 10 users

        $this->command->info('User Seeding Completed.');
    }
}
