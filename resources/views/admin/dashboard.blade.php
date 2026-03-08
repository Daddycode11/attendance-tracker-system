@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

<div class="page-header">
    <div>
        <h1>Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->username }}! 👋</h1>
        <p>Here's what's happening today — {{ now()->format('l, F j, Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.attendance.create') }}" class="btn btn-outline">
            <i class="fa-solid fa-plus"></i> Manual Entry
        </a>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-accent">
            <i class="fa-solid fa-user-plus"></i> Add Employee
        </a>
    </div>
</div>

{{-- ── STAT CARDS ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:#fff3ed;"><i class="fa-solid fa-users" style="color:var(--accent);"></i></div>
            <span class="stat-delta delta-up">Total</span>
        </div>
        <div class="stat-num">{{ $totalEmployees }}</div>
        <div class="stat-label">Total Employees</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:#dcfce7;"><i class="fa-solid fa-circle-check" style="color:#16a34a;"></i></div>
            <span class="stat-delta delta-up">Today</span>
        </div>
        <div class="stat-num" style="color:#16a34a;">{{ $presentToday }}</div>
        <div class="stat-label">Present Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:#fee2e2;"><i class="fa-solid fa-circle-xmark" style="color:#dc2626;"></i></div>
            <span class="stat-delta delta-down">Today</span>
        </div>
        <div class="stat-num" style="color:#dc2626;">{{ $absentToday }}</div>
        <div class="stat-label">Absent Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:#fef3c7;"><i class="fa-solid fa-clock" style="color:#d97706;"></i></div>
            <span class="stat-delta delta-warn">Today</span>
        </div>
        <div class="stat-num" style="color:#d97706;">{{ $lateToday }}</div>
        <div class="stat-label">Late Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:#ede9fe;"><i class="fa-solid fa-calendar-xmark" style="color:#7c3aed;"></i></div>
            <span class="stat-delta {{ $pendingLeaves > 0 ? 'delta-warn' : 'delta-up' }}">Pending</span>
        </div>
        <div class="stat-num" style="color:#7c3aed;">{{ $pendingLeaves }}</div>
        <div class="stat-label">Leave Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:#eff6ff;"><i class="fa-solid fa-stopwatch" style="color:#2563eb;"></i></div>
            <span class="stat-delta delta-up">This Month</span>
        </div>
        <div class="stat-num" style="color:#2563eb;">{{ round($monthlyOT / 60, 1) }}h</div>
        <div class="stat-label">Overtime This Month</div>
    </div>
</div>

{{-- ── TWO COLUMN ── --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;" class="dash-grid">

    {{-- Today's Attendance --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-list-check" style="color:var(--accent);"></i> &nbsp;Today's Attendance</span>
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrap">
            @if($recentAttendance->count())
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>In (AM)</th>
                        <th>Out (Lunch)</th>
                        <th>In (PM)</th>
                        <th>Out (Final)</th>
                        <th>Late</th>
                        <th>OT</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendance as $att)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $att->employee->name }}</div>
                            <div style="font-size:.72rem;color:var(--muted);">{{ $att->employee->employee_id }}</div>
                        </td>
                        <td>{{ $att->time_in_am ? \Carbon\Carbon::parse($att->time_in_am)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_out_lunch ? \Carbon\Carbon::parse($att->time_out_lunch)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_in_pm ? \Carbon\Carbon::parse($att->time_in_pm)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_out_final ? \Carbon\Carbon::parse($att->time_out_final)->format('h:i A') : '—' }}</td>
                        <td>
                            @if($att->late_minutes > 0)
                                <span style="color:#d97706;font-weight:600;">{{ $att->late_minutes }}m</span>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($att->overtime_minutes > 0)
                                <span style="color:#7c3aed;font-weight:600;">{{ $att->overtime_minutes }}m</span>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $sc = match($att->status) {
                                    'Present'    => 'badge-present',
                                    'Late'       => 'badge-late',
                                    'Absent'     => 'badge-absent',
                                    'Half-day'   => 'badge-half',
                                    default      => 'badge-half',
                                };
                            @endphp
                            <span class="badge {{ $sc }}">{{ $att->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fa-regular fa-clock"></i>
                <h3>No attendance yet</h3>
                <p>No employees have timed in today.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Right column --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Still In --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-person-walking-arrow-right" style="color:#16a34a;"></i> &nbsp;Still In</span>
                <span class="badge badge-present">{{ $stillIn->count() }}</span>
            </div>
            <div class="card-body" style="padding:14px 16px;">
                @forelse($stillIn as $s)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f1ede6;">
                    <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);
                        display:flex;align-items:center;justify-content:center;color:#fff;font-size:.7rem;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($s->employee->name, 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:.82rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $s->employee->name }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">
                            IN: {{ \Carbon\Carbon::parse($s->time_in_am)->format('h:i A') }}
                        </div>
                    </div>
                    <span class="badge badge-present">In</span>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--muted);font-size:.84rem;">
                    No employees currently in.
                </div>
                @endforelse
            </div>
        </div>

        {{-- 7-day trend --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-chart-line" style="color:var(--accent);"></i> &nbsp;7-Day Trend</span>
            </div>
            <div class="card-body">
                @foreach($trend as $t)
                <div style="margin-bottom:10px;">
                    <div style="display:flex;justify-content:space-between;font-size:.74rem;color:var(--muted);margin-bottom:4px;">
                        <span>{{ $t['date'] }}</span>
                        <span>{{ $t['present'] }}/{{ $t['present'] + $t['absent'] }}</span>
                    </div>
                    <div style="background:#f1ede6;border-radius:4px;height:7px;overflow:hidden;">
                        @php
                            $total = $t['present'] + $t['absent'];
                            $pct = $total > 0 ? round(($t['present'] / $total) * 100) : 0;
                        @endphp
                        <div style="background:var(--accent);width:{{ $pct }}%;height:100%;border-radius:4px;transition:width .5s;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick links --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Quick Actions</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('admin.employees.create') }}" class="btn btn-outline w-100">
                    <i class="fa-solid fa-user-plus"></i> Add Employee
                </a>
                <a href="{{ route('admin.attendance.create') }}" class="btn btn-outline w-100">
                    <i class="fa-solid fa-calendar-plus"></i> Manual Attendance
                </a>
                <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline w-100">
                    <i class="fa-solid fa-peso-sign"></i> Payroll
                </a>
                @if($pendingLeaves > 0)
                <a href="{{ route('admin.leaves.index', ['status' => 'Pending']) }}" class="btn btn-warning w-100">
                    <i class="fa-solid fa-bell"></i> {{ $pendingLeaves }} Pending Leave{{ $pendingLeaves > 1 ? 's' : '' }}
                </a>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@section('extra_styles')
<style>
@media(max-width:900px){
    .dash-grid { grid-template-columns: 1fr !important; }
}
@media(max-width:768px){
    .dash-grid { gap: 14px !important; }
}
</style>
@endsection