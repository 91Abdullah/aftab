<?php

use Illuminate\Database\Seeder;

class NumbersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('numbers')->insert([
            'number' => '03132299902',
            'status' => rand(0,1),
        ]);
    }
}
