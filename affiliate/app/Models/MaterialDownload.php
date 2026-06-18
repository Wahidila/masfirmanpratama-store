<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialDownload extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'affiliator_id',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function affiliator(): BelongsTo
    {
        return $this->belongsTo(Affiliator::class);
    }
}
