<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            ['key' => 'server_address', 'value' => '10.0.0.19'],
            ['key' => 'wss_comm_port', 'value' => '5061'],
            ['key' => 'wss_socket_port', 'value' => '8089'],
            ['key' => 'auto_answer', 'value' => 'false']
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
