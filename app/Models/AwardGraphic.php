<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class AwardGraphic extends Model
{
    use HasFactory;
    use Mediable;

    protected $fillable = [
        'alt_text',
        'public_asset_path',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'status' => 'integer',
    ];

    public function imageUrl(): ?string
    {
        $media = $this->firstMedia('award');
        if ($media) {
            return Team::publicUrlForMedia($media);
        }

        if ($this->public_asset_path) {
            return asset($this->public_asset_path);
        }

        return null;
    }

    /** True when the preview uses a theme file under public/ (no uploaded media yet). */
    public function usesPublicAssetPath(): bool
    {
        return (bool) $this->public_asset_path && ! $this->firstMedia('award');
    }
}
