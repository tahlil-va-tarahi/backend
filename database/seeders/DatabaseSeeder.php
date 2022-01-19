<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('admin'),
            'is_admin' => 1,
        ]);
        User::create([
            'name' => 'user',
            'email' => 'user@user.user',
            'password' => Hash::make('user'),
        ]);

        Category::create([
            'title' => 'طبیعت'
        ]);
        Category::create([
            'title' => 'تکنولژوی'
        ]);
        Category::create([
            'title' => 'انتزاعی'
        ]);

    }
}
