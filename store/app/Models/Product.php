<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'type',
        'title',
        'price',
        'stock',
        'status',
        'image_path',
        'description',
        'meta_seo',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'meta_seo' => 'array',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function installmentSchemes(): HasMany
    {
        return $this->hasMany(InstallmentScheme::class);
    }

    /**
     * Resolve route binding by slug instead of id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
