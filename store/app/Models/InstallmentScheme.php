<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'course_id',
        'name',
        'dp_pct',
        'n_installments',
        'interval_days',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'dp_pct' => 'decimal:2',
            'n_installments' => 'integer',
            'interval_days' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('active', true);
    }

    /**
     * Schemes for a given product: product-specific + global (product_id=null).
     * Pass null to get global-only.
     */
    public function scopeForProduct(Builder $q, ?int $productId): Builder
    {
        if ($productId === null) {
            return $q->whereNull('product_id');
        }

        return $q->where(function ($inner) use ($productId) {
            $inner->whereNull('product_id')->orWhere('product_id', $productId);
        });
    }

    /**
     * Schemes for a given course: course-specific + global (course_id=null).
     * Pass null to get global-only.
     */
    public function scopeForCourse(Builder $q, ?int $courseId): Builder
    {
        if ($courseId === null) {
            return $q->whereNull('course_id');
        }

        return $q->where(function ($inner) use ($courseId) {
            $inner->whereNull('course_id')->orWhere('course_id', $courseId);
        });
    }
}
