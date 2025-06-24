<?php

namespace App\Policies;

use App\Models\TerrainImage;
use App\Models\User;

class TerrainImagePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, TerrainImage $terrainImage): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, TerrainImage $terrainImage): bool
    {
        return true;
    }

    public function delete(?User $user, TerrainImage $terrainImage): bool
    {
        return true;
    }

    public function restore(?User $user, TerrainImage $terrainImage): bool
    {
        return true;
    }

    public function forceDelete(?User $user, TerrainImage $terrainImage): bool
    {
        return true;
    }
}