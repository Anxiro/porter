<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create(['name' => 'home', 'value' => '']);
        Setting::create(['name' => 'tld', 'value' => 'test']);
        Setting::create(['name' => 'use_mysql', 'value' => 'on']);
        Setting::create(['name' => 'use_redis', 'value' => 'on']);
        Setting::create(['name' => 'db_host', 'value' => 'host.docker.internal']);
    }
}
