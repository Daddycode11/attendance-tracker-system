<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('employee_id', 'like', "%$s%")
                  ->orWhere('department', 'like', "%$s%")
                  ->orWhere('position', 'like', "%$s%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $employees   = $query->orderBy('name')->paginate(15)->withQueryString();
        $departments = Employee::distinct()->pluck('department')->filter()->sort()->values();

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Employee::distinct()->pluck('department')->filter()->sort()->values();
        $positions   = Employee::distinct()->pluck('position')->filter()->sort()->values();
        $nextEmployeeId = $this->generateEmployeeId();
        return view('admin.employees.create', compact('departments', 'positions', 'nextEmployeeId'));
    }

    /**
     * Generate the next sequential employee ID (EMP-001, EMP-002, …)
     */
    private function generateEmployeeId(): string
    {
        $last = Employee::where('employee_id', 'like', 'EMP-%')
            ->orderByRaw("CAST(SUBSTRING(employee_id, 5) AS UNSIGNED) DESC")
            ->value('employee_id');

        $nextNum = $last ? ((int) substr($last, 4)) + 1 : 1;

        return 'EMP-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'department'   => 'required|string|max:50',
            'position'     => 'required|string|max:50',
            'basic_salary' => 'required|numeric|min:0',
            'username'     => 'required|unique:users,username|min:4',
            'password'     => 'required|min:8|confirmed',
        ]);

        $employee = Employee::create([
            'employee_id'  => $this->generateEmployeeId(),
            'name'         => $request->name,
            'department'   => $request->department,
            'position'     => $request->position,
            'basic_salary' => $request->basic_salary,
        ]);

        User::create([
            'employee_id' => $employee->id,
            'username'    => $request->username,
            'password'    => $request->password,
            'role'        => $request->role ?? 'Employee',
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee {$employee->name} created successfully.");
    }

    public function show(Employee $employee)
    {
        $employee->load('user', 'attendances', 'payrolls', 'leaves');
        $recentAttendance = $employee->attendances()->orderByDesc('date')->take(30)->get();
        return view('admin.employees.show', compact('employee', 'recentAttendance'));
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');
        $departments = Employee::distinct()->pluck('department')->filter()->sort()->values();
        $positions   = Employee::distinct()->pluck('position')->filter()->sort()->values();
        return view('admin.employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'department'   => 'required|string|max:50',
            'position'     => 'required|string|max:50',
            'basic_salary' => 'required|numeric|min:0',
            'username'     => 'required|unique:users,username,' . optional($employee->user)->id . '|min:4',
        ]);

        $employee->update([
            'name'         => $request->name,
            'department'   => $request->department,
            'position'     => $request->position,
            'basic_salary' => $request->basic_salary,
        ]);

        if ($employee->user) {
            $userData = ['username' => $request->username, 'role' => $request->role ?? $employee->user->role];
            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $userData['password'] = $request->password;
            }
            $employee->user->update($userData);
        }

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee {$employee->name} updated successfully.");
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        $employee->user?->delete();
        $employee->delete();
        return redirect()->route('admin.employees.index')
            ->with('success', "Employee {$name} deleted successfully.");
    }

    // Employee-facing dashboard
    public function dashboard()
    {
        $employee   = auth()->user()->employee;
        $today      = now()->toDateString();
        $attendance = $employee?->attendances()->whereDate('date', $today)->first();
        return view('employee.dashboard', compact('employee', 'attendance'));
    }
}