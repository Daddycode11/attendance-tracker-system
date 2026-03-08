<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'description'];

    public function employees()
    {
        return \App\Models\Employee::where('department', $this->name)->get();
    }
}
