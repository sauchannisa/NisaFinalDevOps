<?php

namespace App\Policies;

use App\Models\Terrain;
use App\Models\User;

class TerrainPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Terrain $terrain): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, Terrain $terrain): bool
    {
        return true;
    }

    public function delete(?User $user, Terrain $terrain): bool
    {
        return true;
    }

    public function restore(?User $user, Terrain $terrain): bool
    {
        return true;
    }

    public function forceDelete(?User $user, Terrain $terrain): bool
    {
        return true;
    }
}