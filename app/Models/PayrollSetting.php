<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = ['working_days_per_month', 'working_hours_per_day', 'ot_rate_multiplier', 'late_grace_minutes'];

    protected $casts = [
        'ot_rate_multiplier' => 'decimal:2',
    ];

    /**
     * Get the current settings (singleton pattern — always row id=1).
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'working_days_per_month' => 22,
            'working_hours_per_day'  => 8,
            'ot_rate_multiplier'     => 1.25,
            'late_grace_minutes'     => 0,
        ]);
    }
}
