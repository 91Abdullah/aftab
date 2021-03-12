<?php

use Illuminate\Database\Seeder;

class genNumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('gen_nums')->insert([
            'number' => '03132299902',
            'status' => rand(0,1),
        ]);
    }
}
