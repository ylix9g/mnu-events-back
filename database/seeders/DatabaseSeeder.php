<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'login' => 'admin',
            'password' => Hash::make('admin'),
        ]);

        Category::create([
            'order' => 10,
            'name' => 'Гостевые лекции',
        ]);

        Category::create([
            'order' => 20,
            'name' => 'Развлекательные мероприятия',
        ]);

        Category::create([
            'order' => 30,
            'name' => 'Социальные мероприятия',
        ]);

        Category::create([
            'order' => 40,
            'name' => 'Образовательные мероприятия',
        ]);
    }
}
