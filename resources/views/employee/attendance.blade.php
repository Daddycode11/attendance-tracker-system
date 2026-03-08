@extends('layouts.employee')
@section('title','My Attendance')
@section('breadcrumb','Attendance')

@section('content')
<div class="page-header">
    <div>
        <h1>My Attendance</h1>
        <p>View your attendance history and time in/out for today.</p>
    </div>
    <div class="page-header-actions">
        <button onclick="printAttendance()" class="btn btn-outline">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;" class="att-grid">

    {{-- Attendance History --}}
    <div class="card" id="printArea">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-list-check no-print" style="color:var(--accent);"></i> &nbsp;Attendance History — {{ $employee->name }}</span>
        </div>
        <div class="table-wrap">
            @if($history->count())
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
                    @foreach($history as $att)
                    <tr style="{{ $att->date->isToday() ? 'background:#fffbeb;' : '' }}">
                        <td style="white-space:nowrap;">
                            {{ $att->date->format('M d, Y') }}
                            @if($att->date->isToday())
                            <span class="badge" style="background:var(--accent)22;color:var(--accent);font-size:.6rem;margin-left:4px;">TODAY</span>
                            @endif
                        </td>
                        <td>{{ $att->time_in_am ? \Carbon\Carbon::parse($att->time_in_am)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_out_lunch ? \Carbon\Carbon::parse($att->time_out_lunch)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_in_pm ? \Carbon\Carbon::parse($att->time_in_pm)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->time_out_final ? \Carbon\Carbon::parse($att->time_out_final)->format('h:i A') : '—' }}</td>
                        <td>
                            @if($att->late_minutes > 0)
                            <span style="color:var(--warning);font-weight:600;">{{ $att->late_minutes }}m</span>
                            @else — @endif
                        </td>
                        <td>
                            @if($att->overtime_minutes > 0)
                            <span style="color:var(--info);font-weight:600;">{{ $att->overtime_minutes }}m</span>
                            @else — @endif
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
            <div class="empty-state">
                <i class="fa-solid fa-calendar-xmark"></i>
                <h3>No attendance records yet</h3>
                <p>Your attendance will appear here once you start tapping in.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Time Tap Panel --}}
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
        .att-grid {
            grid-template-columns: 1fr !important;
        }
        .att-grid > .card:last-child {
            order: -1; /* Time Tap panel first on mobile */
        }
    }
    @media (max-width: 480px) {
        #liveClock { font-size: 1.8rem !important; }
        .att-grid .btn-lg { padding: 12px 20px !important; font-size: .9rem !important; }
        .email-modal { max-width: 100% !important; border-radius: 10px !important; }
        .email-modal-body { padding: 16px !important; }
        .email-modal-header { padding: 14px 16px !important; }
    }

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
            <p>Enter the email address where you'd like to receive your recent attendance report.</p>
            <form method="POST" action="{{ route('employee.attendance.email') }}">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g. you@email.com" required>
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

function printAttendance() {
    const table = document.querySelector('#printArea');
    if (!table) { window.print(); return; }
    window.print();
}
</script>
@endsection