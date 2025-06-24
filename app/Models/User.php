<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Terrain relationships
    public function ownedTerrains(): HasMany
    {
        return $this->hasMany(Terrain::class, 'owner_id');
    }

    // Booking relationships
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'renter_id');
    }

    public function terrainBookings(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Terrain::class, 'owner_id', 'terrain_id');
    }

    // Review relationships
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Favorite relationships
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteTerrains()
    {
        return $this->belongsToMany(Terrain::class, 'favorites');
    }
}