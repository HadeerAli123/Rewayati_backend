<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'), // Encrypt the password
            'username' => 'testuser123',
               'gender' => 'female',      
                 'role' => 'user',  
        ]);

    }
}
