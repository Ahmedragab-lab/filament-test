<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Setting::truncate();
        Setting::create(['key' => 'name','value' => 'الجامعه الاسلاميه المفتوحه']);
        Setting::create(['key' => 'email','value' => 'test@gmail.com',]);
        Setting::create(['key' => 'phone','value' => '+20 102130303030',]);
        Setting::create(['key' => 'whatsup','value' => 'https://wa.me/+20 102130303030',]);
        Setting::create(['key' => 'youtube','value' => 'https://www.youtube.com',]);
        Setting::create(['key' => 'telegram','value' => 'https://web.telegram.org/a/',]);
        Setting::create(['key' => 'facebook','value' => 'https://facebook.com/',]);
        Setting::create(['key' => 'instagram','value' => 'https://instagram.com/',]);
        Setting::create(['key' => 'tiktok','value' => 'https://www.tiktok.com/ar/',]);
        Setting::create(['key' => 'twitter','value' => 'https://twitter.com/',]);
        Setting::create(['key' => 'logo','value' => 'settings/logo.png',]);
        Setting::create(['key' => 'image','value' => 'settings/logo2.png',]);
        Setting::create(['key' => 'tax','value' => '40',]);
    }
}
