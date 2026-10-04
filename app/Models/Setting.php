<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value with optional fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->find($key);

        if ($setting === null) {
            return $default;
        }

        $decoded = json_decode((string) $setting->value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        $storedValue = is_array($value) || is_object($value)
            ? json_encode($value)
            : (string) $value;

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue]
        );
    }
}
