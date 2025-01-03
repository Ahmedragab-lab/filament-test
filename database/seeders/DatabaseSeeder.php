<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Symfony\Component\String\b;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Ahmed',
        //     'email' => 'admin@admin.com',
        //     'password' => bcrypt('123456'),
        // ]);

        // php artisan shield:install admin
        // php artisan shield:generate --all
        $this->call([
            ShieldSeeder::class,
            UserSeeder::class,
            // SettingSeeder::class
        ]);
    }
}
