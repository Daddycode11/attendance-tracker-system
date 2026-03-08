@extends('layouts.employee')
@section('title','My Leaves')
@section('breadcrumb','Leaves')

@section('content')
<div class="page-header">
    <div>
        <h1>My Leave Requests</h1>
        <p>Submit and track your leave requests.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;" class="leaves-grid">

    {{-- Leave History --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-calendar-check" style="color:var(--accent);"></i> &nbsp;Leave History</span>
        </div>
        <div class="table-wrap">
            @if($leaves->count())
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Admin Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        <td>
                            @php $ti = ['Sick'=>'fa-thermometer-half','Vacation'=>'fa-umbrella-beach','Others'=>'fa-ellipsis']; @endphp
                            <i class="fa-solid {{ $ti[$leave->leave_type] ?? 'fa-ellipsis' }}" style="margin-right:4px;color:var(--muted);"></i>
                            {{ $leave->leave_type }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}</td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $leave->reason ?? '—' }}</td>
                        <td>
                            @php $lc = ['Approved'=>'#16a34a','Pending'=>'#d97706','Rejected'=>'#dc2626']; @endphp
                            <span class="badge" style="background:{{ $lc[$leave->status] ?? '#6b7280' }}22;color:{{ $lc[$leave->status] ?? '#6b7280' }};">
                                {{ $leave->status }}
                            </span>
                        </td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $leave->admin_remarks ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fa-solid fa-calendar-xmark"></i>
                <h3>No leave requests</h3>
                <p>Submit your first leave request using the form.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Submit Leave --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus" style="color:var(--accent);"></i> &nbsp;Request Leave</span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;font-size:.82rem;color:#991b1b;">
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('employee.leaves.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Leave Type <span style="color:var(--danger);">*</span></label>
                    <select name="leave_type" class="form-control" required>
                        <option value="Sick" {{ old('leave_type') == 'Sick' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="Vacation" {{ old('leave_type') == 'Vacation' ? 'selected' : '' }}>Vacation Leave</option>
                        <option value="Others" {{ old('leave_type') == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Start Date <span style="color:var(--danger);">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">End Date <span style="color:var(--danger);">*</span></label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Optional reason for your leave...">{{ old('reason') }}</textarea>
                </div>

                <button type="submit" class="btn btn-accent" style="width:100%;margin-top:8px;">
                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .leaves-grid {
            grid-template-columns: 1fr !important;
        }
        .leaves-grid > .card:last-child {
            order: -1; /* Request form first on mobile */
        }
    }
    @media (max-width: 480px) {
        .leaves-grid .btn { font-size: .78rem !important; padding: 10px 14px !important; }
    }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px; }
    .form-control {
        width: 100%; padding: 10px 14px; border: 1.5px solid var(--border);
        border-radius: 8px; font-size: .88rem; font-family: inherit;
        background: var(--white); transition: border-color .2s;
    }
    .form-control:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(232,93,38,.1); }
    textarea.form-control { resize: vertical; }
</style>
@endsection
