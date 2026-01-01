<?php

namespace Database\Seeders;

use App\Models\blogs;
use App\Models\hastags;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'avatar' => '👨‍💻',
            'bio' => 'Mobile developer passionate about React Native',
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'avatar' => '👩‍💼',
            'bio' => 'Full-stack developer and tech writer',
        ]);

        // Create hashtags
        $reactnative = hastags::create(['name' => '#reactnative', 'usage_count' => 0]);
        $mobile = hastags::create(['name' => '#mobile', 'usage_count' => 0]);
        $javascript = hastags::create(['name' => '#javascript', 'usage_count' => 0]);
        $design = hastags::create(['name' => '#design', 'usage_count' => 0]);

        // Create blogs
        $blog1 = blogs::create([
            'user_id' => $user1->id,
            'title' => 'Getting Started with React Native',
            'content' => 'React Native is an amazing framework for building mobile apps...',
            'likes' => 42,
        ]);
        $blog1->hashtags()->attach([$reactnative->id, $mobile->id, $javascript->id]);

        $blog2 = blogs::create([
            'user_id' => $user2->id,
            'title' => 'Understanding State Management',
            'content' => 'State management is crucial in modern app development...',
            'likes' => 38,
        ]);
        $blog2->hashtags()->attach([$reactnative->id, $javascript->id]);

        // Update hashtag counts
        hastags::all()->each(function ($hashtag) {
            $hashtag->usage_count = $hashtag->blogs()->count();
            $hashtag->save();
        });
    }
}
