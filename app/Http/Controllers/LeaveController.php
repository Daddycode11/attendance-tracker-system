<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Employee;

class LeaveController extends Controller
{
    // ── ADMIN: All leaves
    public function index(Request $request)
    {
        $query = Leave::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $leaves    = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $employees = Employee::orderBy('name')->get();

        return view('admin.leaves.index', compact('leaves', 'employees'));
    }

    public function show(Leave $leave)
    {
        $leave->load('employee');
        return view('admin.leaves.show', compact('leave'));
    }

    // Admin creates leave on behalf
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('admin.leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|in:Sick,Vacation,Others',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string|max:500',
            'status'      => 'required|in:Pending,Approved,Rejected',
        ]);

        Leave::create($request->only(['employee_id','leave_type','start_date','end_date','reason','status']));

        return redirect()->route('admin.leaves.index')->with('success', 'Leave record created.');
    }

    public function edit(Leave $leave)
    {
        $leave->load('employee');
        $employees = Employee::orderBy('name')->get();
        return view('admin.leaves.edit', compact('leave', 'employees'));
    }

    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'leave_type' => 'required|in:Sick,Vacation,Others',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:Pending,Approved,Rejected',
        ]);

        $leave->update($request->only(['leave_type','start_date','end_date','reason','status']));

        return redirect()->route('admin.leaves.index')->with('success', 'Leave updated.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return back()->with('success', 'Leave record deleted.');
    }

    // Quick approve/reject
    public function approve(Request $request, Leave $leave)
    {
        $request->validate(['admin_remarks' => 'nullable|string|max:500']);
        $leave->update([
            'status' => 'Approved',
            'admin_remarks' => $request->admin_remarks,
        ]);
        return back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate(['admin_remarks' => 'nullable|string|max:500']);
        $leave->update([
            'status' => 'Rejected',
            'admin_remarks' => $request->admin_remarks,
        ]);
        return back()->with('success', 'Leave rejected.');
    }

    // ── EMPLOYEE: Own leaves
    public function employeeLeaves()
    {
        $employee = auth()->user()->employee;
        $leaves   = Leave::where('employee_id', $employee->id)->orderByDesc('created_at')->get();
        return view('employee.leaves', compact('leaves', 'employee'));
    }

    public function employeeStore(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:Sick,Vacation,Others',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:500',
        ]);

        Leave::create([
            'employee_id' => auth()->user()->employee->id,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason,
            'status'      => 'Pending',
        ]);

        return back()->with('success', 'Leave request submitted.');
    }
}