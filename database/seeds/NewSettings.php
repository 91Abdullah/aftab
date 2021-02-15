<?php

use Illuminate\Database\Seeder;

class NewSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!\App\Setting::query()->where('key', 'endpoint')->exists()) {
            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'key' => 'endpoint',
                'value' => 'TCL-endpoint',
                'type' => 'text'
            ]);
        }
    }
}
