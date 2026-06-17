<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'file_path',
        'thumbnail',
        'file_size',
        'allowed_types',
        'download_count',
        'is_active',
    ];

    protected $casts = [
        'allowed_types' => 'array',
        'is_active' => 'boolean',
    ];

    public function downloads(): HasMany
    {
        return $this->hasMany(MaterialDownload::class);
    }

    public function isAccessibleBy(Affiliator $affiliator): bool
    {
        if (empty($this->allowed_types)) {
            return true;
        }

        return in_array($affiliator->affiliator_type_id, $this->allowed_types);
    }
}
