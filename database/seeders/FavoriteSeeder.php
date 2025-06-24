<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $terrains = Terrain::all();

        $users->each(function ($user) use ($terrains) {
            // Each user favorites 0-5 terrains
            $favoriteCount = rand(0, 5);
            $userTerrains = $terrains->where('owner_id', '!=', $user->id)->random($favoriteCount);

            foreach ($userTerrains as $terrain) {
                Favorite::factory()->create([
                    'user_id' => $user->id,
                    'terrain_id' => $terrain->id,
                ]);
            }
        });
    }
}