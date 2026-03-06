<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',                    // stored as date: 2026-03-01
        'total_days_present',
        'total_late_minutes',
        'total_overtime_minutes',
        'absent_days',
        'basic_salary',
        'overtime_pay',
        'deductions',
        'net_salary',
    ];

    protected $casts = [
        'month'        => 'date',
        'basic_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'deductions'   => 'decimal:2',
        'net_salary'   => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}