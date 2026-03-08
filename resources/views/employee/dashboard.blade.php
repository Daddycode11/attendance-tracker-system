@extends('layouts.employee')
@section('title','Dashboard')
@section('breadcrumb','Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ $employee->name ?? 'Employee' }}! 👋</h1>
        <p>{{ now()->format('l, F j, Y') }}</p>
    </div>
</div>

{{-- Quick Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:36px;height:36px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-circle-check" style="color:var(--success);"></i>
            </div>
        </div>
        <div class="stat-num" style="color:var(--success);">
            {{ $employee ? $employee->attendances()->whereIn('status',['Present','Late'])->whereMonth('date', now()->month)->count() : 0 }}
        </div>
        <div class="stat-label">Days Present (This Month)</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:36px;height:36px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-clock" style="color:var(--warning);"></i>
            </div>
        </div>
        <div class="stat-num" style="color:var(--warning);">
            {{ $employee ? $employee->attendances()->where('status','Late')->whereMonth('date', now()->month)->count() : 0 }}
        </div>
        <div class="stat-label">Times Late (This Month)</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-stopwatch" style="color:var(--info);"></i>
            </div>
        </div>
        <div class="stat-num" style="color:var(--info);">
            {{ $employee ? round($employee->attendances()->whereMonth('date', now()->month)->sum('overtime_minutes') / 60, 1) : 0 }}h
        </div>
        <div class="stat-label">Overtime (This Month)</div>
    </div>
</div>

{{-- Today's Attendance + Time Tap --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;" class="dash-grid">

    {{-- Today's Status --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-calendar-day" style="color:var(--accent);"></i> &nbsp;Today's Attendance</span>
            <a href="{{ route('employee.attendance') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        @if($attendance)
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
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
                    <tr>
                        <td>{{ $attendance->time_in_am ? \Carbon\Carbon::parse($attendance->time_in_am)->format('h:i A') : '—' }}</td>
                        <td>{{ $attendance->time_out_lunch ? \Carbon\Carbon::parse($attendance->time_out_lunch)->format('h:i A') : '—' }}</td>
                        <td>{{ $attendance->time_in_pm ? \Carbon\Carbon::parse($attendance->time_in_pm)->format('h:i A') : '—' }}</td>
                        <td>{{ $attendance->time_out_final ? \Carbon\Carbon::parse($attendance->time_out_final)->format('h:i A') : '—' }}</td>
                        <td>
                            @if($attendance->late_minutes > 0)
                            <span style="color:var(--warning);font-weight:600;">{{ $attendance->late_minutes }}m</span>
                            @else — @endif
                        </td>
                        <td>
                            @if($attendance->overtime_minutes > 0)
                            <span style="color:var(--info);font-weight:600;">{{ $attendance->overtime_minutes }}m</span>
                            @else — @endif
                        </td>
                        <td>
                            @php
                                $sc = ['Present'=>'#16a34a','Late'=>'#d97706','Absent'=>'#dc2626','Half-day'=>'#7c3aed','Incomplete'=>'#6b7280'];
                            @endphp
                            <span class="badge" style="background:{{ $sc[$attendance->status] ?? '#6b7280' }}22;color:{{ $sc[$attendance->status] ?? '#6b7280' }};">
                                {{ $attendance->status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="card-body" style="text-align:center;padding:40px;">
            <i class="fa-solid fa-clock" style="font-size:2rem;color:var(--muted);"></i>
            <p style="margin-top:10px;color:var(--muted);">No attendance record yet today. Tap the button to time in!</p>
        </div>
        @endif
    </div>

    {{-- Time In / Out Panel --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-fingerprint" style="color:var(--accent);"></i> &nbsp;Time Tap</span>
        </div>
        <div class="card-body" style="text-align:center;padding:30px 20px;">
            <div id="liveClock" style="font-family:'Syne',sans-serif;font-size:2.4rem;font-weight:800;color:var(--ink);letter-spacing:-1px;">
                {{ now()->format('h:i:s A') }}
            </div>
            <div style="color:var(--muted);font-size:.82rem;margin-top:4px;">{{ now()->format('l, F j, Y') }}</div>

            @php
                if (!$attendance || is_null($attendance->time_in_am)) {
                    $nextAction = 'Morning Time In';
                    $nextIcon = 'fa-right-to-bracket';
                    $nextColor = 'var(--success)';
                } elseif (is_null($attendance->time_out_lunch)) {
                    $nextAction = 'Lunch Time Out';
                    $nextIcon = 'fa-utensils';
                    $nextColor = 'var(--warning)';
                } elseif (is_null($attendance->time_in_pm)) {
                    $nextAction = 'Afternoon Time In';
                    $nextIcon = 'fa-right-to-bracket';
                    $nextColor = 'var(--info)';
                } elseif (is_null($attendance->time_out_final)) {
                    $nextAction = 'Final Time Out';
                    $nextIcon = 'fa-right-from-bracket';
                    $nextColor = 'var(--danger)';
                } else {
                    $nextAction = null;
                }
            @endphp

            @if($nextAction)
            <div style="margin:20px 0 10px;font-size:.82rem;color:var(--muted);">Next action:</div>
            <div style="font-weight:700;font-size:1rem;margin-bottom:20px;color:{{ $nextColor }};">
                <i class="fa-solid {{ $nextIcon }}"></i> {{ $nextAction }}
            </div>
            <form method="POST" action="{{ route('employee.attendance.tap') }}">
                @csrf
                <button type="submit" class="btn btn-accent btn-lg" style="width:100%;">
                    <i class="fa-solid fa-fingerprint"></i> {{ $nextAction }}
                </button>
            </form>
            @else
            <div style="margin-top:24px;padding:18px;background:#f0fdf4;border-radius:10px;">
                <i class="fa-solid fa-circle-check" style="color:var(--success);font-size:1.5rem;"></i>
                <div style="font-weight:700;margin-top:8px;color:var(--success);">All Done for Today!</div>
                <div style="font-size:.82rem;color:var(--muted);margin-top:4px;">You've completed all time entries.</div>
            </div>
            @endif

            @if($attendance)
            <div style="display:flex;justify-content:center;gap:8px;margin-top:20px;">
                <div title="Morning In" style="width:10px;height:10px;border-radius:50%;background:{{ $attendance->time_in_am ? 'var(--success)' : 'var(--border)' }};"></div>
                <div title="Lunch Out" style="width:10px;height:10px;border-radius:50%;background:{{ $attendance->time_out_lunch ? 'var(--success)' : 'var(--border)' }};"></div>
                <div title="Afternoon In" style="width:10px;height:10px;border-radius:50%;background:{{ $attendance->time_in_pm ? 'var(--success)' : 'var(--border)' }};"></div>
                <div title="Final Out" style="width:10px;height:10px;border-radius:50%;background:{{ $attendance->time_out_final ? 'var(--success)' : 'var(--border)' }};"></div>
            </div>
            <div style="font-size:.7rem;color:var(--muted);margin-top:6px;">AM In → Lunch Out → PM In → Final Out</div>
            @endif
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .dash-grid {
            grid-template-columns: 1fr !important;
        }
        .dash-grid > .card:last-child {
            order: -1; /* Time Tap panel first on mobile */
        }
    }
    @media (max-width: 480px) {
        .dash-grid #liveClock {
            font-size: 1.8rem !important;
        }
        .dash-grid .btn-lg {
            padding: 12px 20px !important;
            font-size: .9rem !important;
        }
        .page-header h1 { font-size: 1.1rem !important; }
    }
</style>

<script>
function updateClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;
    const now = new Date();
    let h = now.getHours(), ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    const m = now.getMinutes().toString().padStart(2, '0');
    const s = now.getSeconds().toString().padStart(2, '0');
    el.textContent = `${h}:${m}:${s} ${ampm}`;
}
updateClock();
setInterval(updateClock, 1000);
</script>
@endsection