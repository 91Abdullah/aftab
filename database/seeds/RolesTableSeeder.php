<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(
            [
                'name' => 'admin',
                'desc' => 'Admin Role'
            ]
        );

        DB::table('roles')->insert([
            [
                'name' => 'agent',
                'desc' => 'Agent Role',
            ]
        ]);

        DB::table('roles')->insert([
            [
                'name' => 'reporter',
                'desc' => 'Reporter Role'
            ]
        ]);
    }
}
