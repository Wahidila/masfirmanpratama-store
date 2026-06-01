<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'price',
        'original_price',
        'status',
        'image_path',
        'badge',
        'badge_icon',
        'category_label',
        'rating',
        'student_count',
        'tagline',
        'installment_available',
        'description',
        'syllabus',
        'schedule',
        'benefits',
        'testimonials',
        'related',
        'meta_seo',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'installment_available' => 'boolean',
            'description' => 'array',
            'syllabus' => 'array',
            'schedule' => 'array',
            'benefits' => 'array',
            'testimonials' => 'array',
            'related' => 'array',
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
     * Scope: only active courses.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Resolve route binding by slug instead of id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
