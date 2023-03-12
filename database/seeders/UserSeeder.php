<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test_user@gmail.com',
            'address'    => 'Arham heights, punjab colony, karachi',
            'dob'        => date("Y-m-d", strtotime('02/25/1994')),
            'password'   => bcrypt('Variable$1'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
