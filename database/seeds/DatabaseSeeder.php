<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");

        DB::table('users')->delete();
        DB::table('access_id')->delete();
       // DB::table('biddings')->delete();

        DB::statement('ALTER TABLE users AUTO_INCREMENT = 0');
        DB::statement('ALTER TABLE access_id AUTO_INCREMENT = 0');
       // DB::statement('ALTER TABLE biddings AUTO_INCREMENT = 0');
        
        DB::statement("SET foreign_key_checks=1");

        $this->call(UsersSeeder::class);
        $this->call(AccessSeeder::class);
        //$this->call(BiddingsSeeder::class);
    }
}
