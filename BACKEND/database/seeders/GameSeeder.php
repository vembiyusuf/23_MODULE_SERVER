<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Game;
use App\Models\GameVersion;
use App\Models\Score;
use Illuminate\Support\Facades\Hash;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 1 user
        $user = User::firstOrCreate([
            'username' => 'testuser',
        ], [
            'password' => Hash::make('password'),
        ]);

        foreach (['Snake Game', 'Space Shooter', 'Flappy Bird'] as $i => $title) {
            $game = Game::create([
                'title' => $title,
                'slug' => strtolower(str_replace(' ', '-', $title)),
                'description' => 'Game seru ' . ($i + 1),
                'created_by' => $user->id,
            ]);

            // Tambahkan 1 versi game
            $version = GameVersion::create([
                'game_id' => $game->id,
                'version' => '1.0.' . $i,
                'description' => 'Versi awal dari ' . $title,
                'thumbnail' => 'https://via.placeholder.com/150?text=' . urlencode($title),
                'created_at' => now()->subDays($i),
            ]);

            // Tambahkan skor dummy
            Score::create([
                'user_id' => $user->id,
                'game_version_id' => $version->id,
                'score' => rand(100, 1000),
            ]);
        }
    }
}
