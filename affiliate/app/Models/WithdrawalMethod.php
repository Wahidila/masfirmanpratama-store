<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WithdrawalMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'is_active',
        'min_withdrawal',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_withdrawal' => 'decimal:2',
    ];

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
