<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ── Fillable ───────────────────────────────
    protected $fillable = [
        'employee_id',
        'username',
        'password',
        'role',         // 'Admin' or 'Employee'
    ];

    // ── Hidden from JSON ───────────────────────
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ── Casts ──────────────────────────────────
    protected $casts = [
        'password' => 'hashed',
    ];

    // ── Relationships ──────────────────────────

    /** The employee profile this account belongs to */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}