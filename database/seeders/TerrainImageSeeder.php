<?php

namespace Database\Seeders;

use App\Models\Terrain;
use App\Models\TerrainImage;
use Illuminate\Database\Seeder;

class TerrainImageSeeder extends Seeder
{
    public function run(): void
    {
        $terrains = Terrain::all();

        $terrains->each(function ($terrain) {
            // Each terrain gets 1-5 additional images
            $imageCount = rand(1, 5);
            
            TerrainImage::factory()
                ->count($imageCount)
                ->create(['terrain_id' => $terrain->id]);
        });
    }
}