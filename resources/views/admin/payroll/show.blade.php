@extends('layouts.admin')
@section('title','Payroll Details')
@section('breadcrumb','Payroll Details')

@section('content')
<div class="page-header">
    <div>
        <h1>Payroll — {{ $payroll->employee->name }}</h1>
        <p>{{ \Carbon\Carbon::parse($payroll->month)->format('F Y') }} &middot; {{ $payroll->employee->employee_id }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.payroll.index', ['month' => \Carbon\Carbon::parse($payroll->month)->format('Y-m')]) }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
        <a href="{{ route('admin.payroll.edit', $payroll) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="form-grid">
    {{-- Summary Card --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-receipt" style="color:var(--accent);"></i> &nbsp;Payroll Summary</span>
        </div>
        <div class="card-body">
            <div class="payroll-row"><span>Basic Salary</span><span style="font-weight:700;">₱{{ number_format($payroll->basic_salary, 2) }}</span></div>
            <div class="payroll-row"><span>Days Present</span><span>{{ $payroll->total_days_present }}</span></div>
            <div class="payroll-row"><span>Absent Days</span><span>{{ $payroll->absent_days }}</span></div>
            <div class="payroll-row"><span>Total Late (min)</span><span style="color:#d97706;">{{ $payroll->total_late_minutes }}</span></div>
            <div class="payroll-row"><span>Total OT (min)</span><span style="color:#7c3aed;">{{ $payroll->total_overtime_minutes }}</span></div>
            <div class="payroll-row" style="border-top:2px solid var(--border);padding-top:14px;margin-top:8px;">
                <span>Overtime Pay</span><span style="color:#16a34a;font-weight:700;">+₱{{ number_format($payroll->overtime_pay, 2) }}</span>
            </div>
            <div class="payroll-row"><span>Deductions</span><span style="color:#dc2626;font-weight:700;">−₱{{ number_format($payroll->deductions, 2) }}</span></div>
            <div class="payroll-row" style="border-top:2px solid var(--accent);padding-top:14px;margin-top:8px;">
                <span style="font-weight:800;font-size:1rem;">Net Salary</span>
                <span style="font-weight:800;font-size:1.1rem;color:var(--accent);">₱{{ number_format($payroll->net_salary, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Attendance Breakdown --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-calendar-days" style="color:var(--accent);"></i> &nbsp;Attendance Breakdown</span>
        </div>
        <div class="table-wrap">
            @if($attendances->count())
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>In (AM)</th>
                        <th>Out (Final)</th>
                        <th>Late</th>
                        <th>OT</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $att)
                    <tr>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($att->date)->format('M d') }}</td>
                        <td>{{ $att->time_in_am ? \Carbon\Carbon::parse($att->time_in_am)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_out_final ? \Carbon\Carbon::parse($att->time_out_final)->format('h:i A') : '—' }}</td>
                        <td>
                            @if($att->late_minutes > 0)
                                <span style="color:#d97706;font-weight:600;">{{ $att->late_minutes }}m</span>
                            @else — @endif
                        </td>
                        <td>
                            @if($att->overtime_minutes > 0)
                                <span style="color:#7c3aed;font-weight:600;">{{ $att->overtime_minutes }}m</span>
                            @else — @endif
                        </td>
                        <td>
                            @php
                                $bc = match($att->status){
                                    'Present'=>'badge-present','Late'=>'badge-late','Absent'=>'badge-absent',default=>'badge-half'
                                };
                            @endphp
                            <span class="badge {{ $bc }}">{{ $att->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i>
                <h3>No attendance records</h3>
                <p>No attendance data for this month.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card-body { padding: 20px; }
    .payroll-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; border-bottom: 1px solid var(--border); font-size: .88rem;
    }
    .payroll-row:last-child { border-bottom: none; }
</style>
@endsection
