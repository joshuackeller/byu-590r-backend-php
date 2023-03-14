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
                'email' => 'josh@email.com',
                'email_verified_at' => null,
                'password' => bcrypt("mypassword"),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Bob Sacamano',
                'email' => 'bobsacamano@email.com',
                'email_verified_at' => null,
                'password' => bcrypt("mypassword"),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
         ];

         User::insert($users);
    }
}
