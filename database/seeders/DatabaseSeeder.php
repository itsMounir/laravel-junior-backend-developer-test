<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Models\Country;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Country::factory(10)->create();
        Industry::factory(10)->create();

        // maybe create a company for each user
        Company::factory(5)->create();

    }
}
