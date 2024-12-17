<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

      
        $this->call(CategorySeeder::class); 
        $this->call(StorySeeder::class);
        //// الكوول ده هو الي بيشغل السيدرس من غير الكول مفيش داتا هتتضاف 
    }
}
