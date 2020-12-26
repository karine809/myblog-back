<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            "firstname" => "Karine",
            "lastname" => "Mesropyan",
            "email" => "kar@mail.ru",
            "password" => Hash::make("222111"),
            "user_type" => "SuperAdmin",
            "user_locked" => false,
            "email_verified" => true

        ]);
    }
}
