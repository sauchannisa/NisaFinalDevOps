<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TerrainImage extends Model
{
    /** @use HasFactory<\Database\Factories\TerrainImageFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'terrain_id',
        'image_path',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function terrain(): BelongsTo
    {
        return $this->belongsTo(Terrain::class);
    }
}
