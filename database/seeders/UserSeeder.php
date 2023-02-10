<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Josh Keller',
                'email' => 'joshuack@byu.edu',
                'email_verified_at' => null,
                'password' => bcrypt("password"),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Bob Sacamano',
                'email' => 'bobsacamano@byu.edu',
                'email_verified_at' => null,
                'password' => bcrypt("password1"),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
         ];

         User::insert($users);
    }
}
