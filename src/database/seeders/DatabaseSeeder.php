<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Post;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Post::factory(30)->create();

        [$first] = User::factory(15)->create()->each(function ($user) {
            Post::factory(random_int(2, 5))->random()->create(['user_id' => $user])->each(function ($post) {
                Comment::factory(random_int(1, 5))->create(['post_id' => $post]);
            });
        });

        $first->update([
            'name' => 'テストユーザー',
            'email' => 'testuser@test.com',
            'password' => Hash::make('hogehoge'),
        ]);
    }
}
