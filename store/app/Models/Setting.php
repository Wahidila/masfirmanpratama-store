<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    /**
     * Tipe yang valid untuk kolom `type`.
     */
    public const TYPES = ['string', 'int', 'bool', 'json', 'array'];

    /**
     * Get a setting value with type casting.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $row = static::where('key', $key)->first();
        if (! $row) {
            return $default;
        }

        return match ($row->type) {
            'int' => (int) $row->value,
            'bool' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode((string) $row->value, true),
            default => $row->value,
        };
    }

    /**
     * Set a setting value with auto-typed serialization.
     */
    public static function setValue(string $key, mixed $value, ?string $type = null): self
    {
        $type ??= match (true) {
            is_bool($value) => 'bool',
            is_int($value) => 'int',
            is_array($value) => 'array',
            default => 'string',
        };

        $stored = match ($type) {
            'bool' => $value ? '1' : '0',
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type],
        );
    }
}
