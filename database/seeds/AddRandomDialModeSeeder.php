<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddRandomDialModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => 'random_mode',
            'value' => se,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
