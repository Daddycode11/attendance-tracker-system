<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',   // Sick | Vacation | Others
        'start_date',
        'end_date',
        'reason',
        'status',       // Pending | Approved | Rejected
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // ── Relationships ──────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}