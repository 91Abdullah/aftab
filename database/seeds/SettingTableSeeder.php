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
            ['key' => 'server_address', 'value' => '192.168.144.152'],
            ['key' => 'wss_comm_port', 'value' => '5160'],
            ['key' => 'wss_socket_port', 'value' => '8089'],
            ['key' => 'auto_answer', 'value' => 'false'],
	    ['key' => 'random_mode', 'value' => '192.168.144.152']
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
