<?php

namespace Database\Seeders;

use App\Models\Language;
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
        Language::updateOrCreate([
           'title' => 'English',
           'code' => 'en',
        ]);

        Language::updateOrCreate([
            'title' => 'Arabic',
            'code' => 'ar',
        ]);
    }
}
