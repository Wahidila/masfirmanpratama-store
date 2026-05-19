<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentScheme extends Model
{
    protected $fillable = [
        'product_id',
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
}
