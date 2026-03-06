<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'department',
        'position',
        'basic_salary',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────

    /** One employee has one login account */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /** One employee has many daily attendance records */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /** One employee has many monthly payroll records */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    /** One employee has many leave requests */
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}