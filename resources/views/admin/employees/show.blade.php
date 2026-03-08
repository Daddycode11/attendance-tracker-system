@extends('layouts.admin')
@section('title', $employee->name)
@section('breadcrumb', 'Employee Details')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $employee->name }}</h1>
        <p>Employee profile &amp; records for <code style="font-size:.85rem;">{{ $employee->employee_id }}</code></p>
    </div>
    <div class="page-header-actions">
        <button onclick="printAttendance()" class="btn btn-outline">
            <i class="fa-solid fa-print"></i> Print
        </button>
        <button onclick="document.getElementById('emailModal').style.display='flex'" class="btn btn-accent">
            <i class="fa-solid fa-envelope"></i> Send Email
        </button>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"
                data-confirm="Delete {{ $employee->name }}? This will also remove their attendance and leave records.">
                <i class="fa-solid fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start;" class="show-grid">

    {{-- Profile Card --}}
    <div class="card">
        <div class="card-body" style="text-align:center;padding:30px 20px;">
            <div style="width:80px;height:80px;border-radius:50%;
                background:linear-gradient(135deg,#667eea,#764ba2);
                display:flex;align-items:center;justify-content:center;
                color:#fff;font-size:1.6rem;font-weight:700;margin:0 auto 16px;">
                {{ strtoupper(substr($employee->name,0,2)) }}
            </div>
            <h2 style="font-size:1.2rem;margin-bottom:4px;">{{ $employee->name }}</h2>
            <div style="color:var(--muted);font-size:.85rem;margin-bottom:16px;">{{ $employee->position ?? '—' }}</div>

            @if($employee->user)
            <span class="badge {{ $employee->user->role === 'Admin' ? 'badge-admin' : 'badge-employee' }}" style="font-size:.78rem;">
                {{ $employee->user->role }}
            </span>
            @endif
        </div>
        <div style="border-top:1px solid var(--border);padding:20px;">
            <div class="profile-row">
                <span class="profile-label"><i class="fa-solid fa-id-badge"></i> Employee ID</span>
                <span class="profile-value">{{ $employee->employee_id }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label"><i class="fa-solid fa-envelope"></i> Email</span>
                <span class="profile-value">{{ $employee->email ?? '—' }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label"><i class="fa-solid fa-building"></i> Department</span>
                <span class="profile-value">{{ $employee->department ?? '—' }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label"><i class="fa-solid fa-briefcase"></i> Position</span>
                <span class="profile-value">{{ $employee->position ?? '—' }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label"><i class="fa-solid fa-peso-sign"></i> Basic Salary</span>
                <span class="profile-value">₱{{ number_format($employee->basic_salary, 2) }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label"><i class="fa-solid fa-user"></i> Username</span>
                <span class="profile-value">{{ $employee->user?->username ?? 'No account' }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label"><i class="fa-solid fa-calendar"></i> Created</span>
                <span class="profile-value">{{ $employee->created_at?->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div>
        {{-- Quick Stats --}}
        <div class="show-stats-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;">
            <div class="card" style="padding:18px;text-align:center;">
                <div style="font-size:1.6rem;font-weight:700;color:var(--success);">
                    {{ $employee->attendances->whereIn('status', ['Present','Late'])->count() }}
                </div>
                <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">Days Present</div>
            </div>
            <div class="card" style="padding:18px;text-align:center;">
                <div style="font-size:1.6rem;font-weight:700;color:var(--warning);">
                    {{ $employee->attendances->where('status', 'Late')->count() }}
                </div>
                <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">Times Late</div>
            </div>
            <div class="card" style="padding:18px;text-align:center;">
                <div style="font-size:1.6rem;font-weight:700;color:var(--info);">
                    {{ $employee->leaves->where('status', 'Approved')->count() }}
                </div>
                <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">Leaves Taken</div>
            </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="card" id="printArea">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-clock no-print" style="color:var(--accent);"></i> &nbsp;Recent Attendance — {{ $employee->name }}</span>
            </div>
            <div class="table-wrap">
                @if($recentAttendance->count())
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
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
                            <td>{{ $att->date->format('M d, Y') }}</td>
                            <td>{{ $att->time_in_am ? \Carbon\Carbon::parse($att->time_in_am)->format('h:i A') : '—' }}</td>
                            <td>{{ $att->time_out_lunch ? \Carbon\Carbon::parse($att->time_out_lunch)->format('h:i A') : '—' }}</td>
                            <td>{{ $att->time_in_pm ? \Carbon\Carbon::parse($att->time_in_pm)->format('h:i A') : '—' }}</td>
                            <td>{{ $att->time_out_final ? \Carbon\Carbon::parse($att->time_out_final)->format('h:i A') : '—' }}</td>
                            <td>
                                @if($att->late_minutes > 0)
                                <span style="color:var(--warning);font-weight:600;">{{ $att->late_minutes }}m</span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($att->overtime_minutes > 0)
                                <span style="color:var(--info);font-weight:600;">{{ $att->overtime_minutes }}m</span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @php
                                    $colors = ['Present'=>'#16a34a','Late'=>'#d97706','Absent'=>'#dc2626','Half-day'=>'#7c3aed','Incomplete'=>'#6b7280'];
                                @endphp
                                <span class="badge" style="background:{{ $colors[$att->status] ?? '#6b7280' }}22;color:{{ $colors[$att->status] ?? '#6b7280' }};">
                                    {{ $att->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state" style="padding:40px;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size:2rem;color:var(--muted);"></i>
                    <p style="margin-top:10px;color:var(--muted);">No attendance records yet.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Leave Requests --}}
        @if($employee->leaves->count())
        <div class="card" style="margin-top:20px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-calendar-check" style="color:var(--accent);"></i> &nbsp;Leave Requests</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->leaves->sortByDesc('start_date')->take(10) as $leave)
                        <tr>
                            <td>{{ $leave->leave_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $leave->reason ?? '—' }}
                            </td>
                            <td>
                                @php
                                    $lc = ['Approved'=>'#16a34a','Pending'=>'#d97706','Rejected'=>'#dc2626'];
                                @endphp
                                <span class="badge" style="background:{{ $lc[$leave->status] ?? '#6b7280' }}22;color:{{ $lc[$leave->status] ?? '#6b7280' }};">
                                    {{ $leave->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .show-grid { grid-template-columns: 1fr !important; }
        .show-stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 480px) {
        .show-stats-grid { grid-template-columns: 1fr !important; }
        .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
        .page-header .btn { width: 100%; justify-content: center; font-size: .82rem; padding: 8px 12px; }
    }
    .card-body { padding: 20px; }
    .profile-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; border-bottom: 1px solid var(--border);
    }
    .profile-row:last-child { border-bottom: none; }
    .profile-label {
        font-size: .82rem; color: var(--muted);
        display: flex; align-items: center; gap: 8px;
    }
    .profile-label i { width: 16px; text-align: center; font-size: .78rem; }
    .profile-value { font-size: .88rem; font-weight: 600; }
    .badge-admin { background: rgba(232,93,38,.12); color: var(--accent); }
    .badge-employee { background: rgba(37,99,235,.12); color: #2563eb; }

    /* Email Modal */
    .email-modal-overlay {
        display: none; position: fixed; inset: 0; z-index: 999;
        background: rgba(0,0,0,.5); backdrop-filter: blur(2px);
        align-items: center; justify-content: center; padding: 16px;
    }
    .email-modal {
        background: #fff; border-radius: 14px; width: 100%; max-width: 420px;
        box-shadow: 0 20px 50px rgba(0,0,0,.2); overflow: hidden;
    }
    .email-modal-header {
        padding: 18px 22px; border-bottom: 1px solid #f0ede6;
        display: flex; justify-content: space-between; align-items: center;
    }
    .email-modal-header h3 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1rem; margin: 0; }
    .email-modal-close {
        background: none; border: none; font-size: 1.2rem; color: #999;
        cursor: pointer; padding: 4px; border-radius: 6px; transition: all .2s;
    }
    .email-modal-close:hover { color: #333; background: #f3f4f6; }
    .email-modal-body { padding: 22px; }
    .email-modal-body p { font-size: .84rem; color: #6b7280; margin-bottom: 16px; }

    /* Print styles */
    @media print {
        body * { visibility: hidden; }
        #printArea, #printArea * { visibility: visible; }
        #printArea {
            position: absolute; top: 0; left: 0; width: 100%;
            background: #fff; padding: 20px;
        }
        #printArea table { min-width: auto !important; }
        .no-print { display: none !important; }
    }
</style>

{{-- Email Modal --}}
<div class="email-modal-overlay" id="emailModal" onclick="if(event.target===this)this.style.display='none'">
    <div class="email-modal">
        <div class="email-modal-header">
            <h3><i class="fa-solid fa-envelope" style="color:var(--accent);"></i> &nbsp;Send Attendance Report</h3>
            <button class="email-modal-close" onclick="document.getElementById('emailModal').style.display='none'">&times;</button>
        </div>
        <div class="email-modal-body">
            <p>Send {{ $employee->name }}'s attendance report to an email address.</p>
            <form method="POST" action="{{ route('admin.employees.email-attendance', $employee) }}">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g. recipient@email.com" value="{{ $employee->email }}" required>
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('emailModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-accent"><i class="fa-solid fa-paper-plane"></i> Send Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function printAttendance() {
    window.print();
}
</script>
@endsection
