<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'time_in_am',
        'time_out_lunch',
        'time_in_pm',
        'time_out_final',
        'late_minutes',
        'undertime_minutes',
        'overtime_minutes',
        'status',           // Present | Late | Absent | Half-day | Incomplete
    ];

    protected $casts = [
        'date'              => 'date',
        'late_minutes'      => 'integer',
        'undertime_minutes' => 'integer',
        'overtime_minutes'  => 'integer',
    ];

    // ── Relationships ──────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}