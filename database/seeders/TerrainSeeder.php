<?php

namespace Database\Seeders;

use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Seeder;

class TerrainSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        // Create terrains for existing users
        $users->each(function ($user) {
            Terrain::factory()
                ->count(rand(1, 3))
                ->create(['owner_id' => $user->id]);
        });

        // Create some additional terrains
        Terrain::factory(20)->create();
    }
}