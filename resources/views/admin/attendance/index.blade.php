{{-- ══════════════════════════════════════════════════════
     FILE: resources/views/admin/attendance/index.blade.php
══════════════════════════════════════════════════════ --}}
@extends('layouts.admin')
@section('title','Attendance')
@section('breadcrumb','Attendance')

@section('content')
<div class="page-header">
    <div>
        <h1>Attendance Records</h1>
        <p>View and manage daily attendance logs.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.attendance.create') }}" class="btn btn-accent">
            <i class="fa-solid fa-plus"></i> Manual Entry
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET">
    <div class="filter-bar">
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ request('date', today()->toDateString()) }}">
        </div>
        <div class="form-group">
            <label>Employee</label>
            <select name="employee_id" class="form-control">
                <option value="">All Employees</option>
                @foreach($employees as $e)
                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">All Status</option>
                @foreach(['Present','Late','Absent','Half-day','Incomplete'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="margin-top:24px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline" style="margin-top:24px;margin-left:6px;">Clear</a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <span class="card-title">{{ $records->total() }} Record{{ $records->total() != 1 ? 's' : '' }}</span>
        <span style="font-size:.78rem;color:var(--muted);">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</span>
    </div>
    <div class="table-wrap">
        @if($records->count())
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>In (AM)</th>
                    <th>Out (Lunch)</th>
                    <th>In (PM)</th>
                    <th>Out (Final)</th>
                    <th>Late</th>
                    <th>OT</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $r->employee->name }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">{{ $r->employee->department }}</div>
                    </td>
                    <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }}</td>
                    <td>{{ $r->time_in_am    ? \Carbon\Carbon::parse($r->time_in_am)->format('h:i A')    : '—' }}</td>
                    <td>{{ $r->time_out_lunch ? \Carbon\Carbon::parse($r->time_out_lunch)->format('h:i A') : '—' }}</td>
                    <td>{{ $r->time_in_pm    ? \Carbon\Carbon::parse($r->time_in_pm)->format('h:i A')    : '—' }}</td>
                    <td>{{ $r->time_out_final ? \Carbon\Carbon::parse($r->time_out_final)->format('h:i A') : '—' }}</td>
                    <td>
                        @if($r->late_minutes > 0)
                            <span style="color:#d97706;font-weight:600;">{{ $r->late_minutes }}m</span>
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($r->overtime_minutes > 0)
                            <span style="color:#7c3aed;font-weight:600;">{{ $r->overtime_minutes }}m</span>
                        @else —
                        @endif
                    </td>
                    <td>
                        @php
                            $bc = match($r->status){
                                'Present'    =>'badge-present',
                                'Late'       =>'badge-late',
                                'Absent'     =>'badge-absent',
                                default      =>'badge-half'
                            };
                        @endphp
                        <span class="badge {{ $bc }}">{{ $r->status }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.attendance.edit', $r) }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.attendance.destroy', $r) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    data-confirm="Delete this attendance record?">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fa-regular fa-calendar-xmark"></i>
            <h3>No records found</h3>
            <p>Try a different date or employee filter.</p>
        </div>
        @endif
    </div>
    @if($records->hasPages())
    <div class="pagination">
        @if(!$records->onFirstPage())
            <a href="{{ $records->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
        @endif
        @foreach($records->getUrlRange(max(1,$records->currentPage()-2), min($records->lastPage(),$records->currentPage()+2)) as $p=>$u)
            <a href="{{ $u }}" class="page-link {{ $p==$records->currentPage()?'active':'' }}">{{ $p }}</a>
        @endforeach
        @if($records->hasMorePages())
            <a href="{{ $records->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
        @endif
    </div>
    @endif
</div>
@endsection


{{-- ═════════════════════════════════════════════════════════
     FILE: resources/views/admin/attendance/create.blade.php
═════════════════════════════════════════════════════════ --}}

{{-- SAVE AS: resources/views/admin/attendance/create.blade.php --}}
{{--
@extends('layouts.admin')
@section('title','Manual Attendance')
@section('breadcrumb','Manual Attendance')

@section('content')
<div class="page-header">
    <div><h1>Manual Attendance Entry</h1><p>Record attendance on behalf of an employee.</p></div>
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div style="max-width:680px;">
<form action="{{ route('admin.attendance.store') }}" method="POST">
@csrf

<div class="card">
    <div class="card-header"><span class="card-title">Attendance Details</span></div>
    <div class="card-body">
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Employee *</label>
                <select name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                    <option value="">— Select Employee —</option>
                    @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ old('employee_id')==$e->id?'selected':'' }}>
                        {{ $e->name }} ({{ $e->employee_id }})
                    </option>
                    @endforeach
                </select>
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Date *</label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                    value="{{ old('date', today()->toDateString()) }}" required>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    @foreach(['Present','Late','Absent','Half-day','Incomplete'] as $s)
                    <option value="{{ $s }}" {{ old('status',$s=='Present'?'Present':'') == $s ? 'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Time In (AM)</label>
                <input type="time" name="time_in_am" class="form-control" value="{{ old('time_in_am','08:00') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Lunch Out</label>
                <input type="time" name="time_out_lunch" class="form-control" value="{{ old('time_out_lunch','12:00') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Time In (PM)</label>
                <input type="time" name="time_in_pm" class="form-control" value="{{ old('time_in_pm','13:00') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Final Time Out</label>
                <input type="time" name="time_out_final" class="form-control" value="{{ old('time_out_final','17:00') }}">
            </div>
        </div>
    </div>
</div>

<div style="margin-top:18px;display:flex;gap:10px;">
    <button type="submit" class="btn btn-accent"><i class="fa-solid fa-plus"></i> Create Record</button>
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline">Cancel</a>
</div>

</form>
</div>
@endsection
--}}


{{-- ════════════════════════════════════════════════════════
     FILE: resources/views/admin/attendance/edit.blade.php
════════════════════════════════════════════════════════ --}}

{{-- SAVE AS: resources/views/admin/attendance/edit.blade.php --}}
{{--
@extends('layouts.admin')
@section('title','Edit Attendance')
@section('breadcrumb','Edit Attendance')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Attendance</h1>
        <p>{{ $attendance->employee->name }} — {{ \Carbon\Carbon::parse($attendance->date)->format('F j, Y') }}</p>
    </div>
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div style="max-width:680px;">
<form action="{{ route('admin.attendance.update', $attendance) }}" method="POST">
@csrf @method('PUT')

<div class="card">
    <div class="card-header"><span class="card-title">Edit Times &amp; Status</span></div>
    <div class="card-body">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Time In (AM)</label>
                <input type="time" name="time_in_am" class="form-control"
                    value="{{ old('time_in_am', $attendance->time_in_am ? \Carbon\Carbon::parse($attendance->time_in_am)->format('H:i') : '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Lunch Out</label>
                <input type="time" name="time_out_lunch" class="form-control"
                    value="{{ old('time_out_lunch', $attendance->time_out_lunch ? \Carbon\Carbon::parse($attendance->time_out_lunch)->format('H:i') : '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Time In (PM)</label>
                <input type="time" name="time_in_pm" class="form-control"
                    value="{{ old('time_in_pm', $attendance->time_in_pm ? \Carbon\Carbon::parse($attendance->time_in_pm)->format('H:i') : '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Final Time Out</label>
                <input type="time" name="time_out_final" class="form-control"
                    value="{{ old('time_out_final', $attendance->time_out_final ? \Carbon\Carbon::parse($attendance->time_out_final)->format('H:i') : '') }}">
            </div>
            <div class="form-group full">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    @foreach(['Present','Late','Absent','Half-day','Incomplete'] as $s)
                    <option value="{{ $s }}" {{ old('status', $attendance->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-top:16px;padding:14px;background:#f8f7f4;border-radius:10px;font-size:.82rem;">
            <strong>Current:</strong>
            Late: {{ $attendance->late_minutes ?? 0 }}min &nbsp;|&nbsp;
            OT: {{ $attendance->overtime_minutes ?? 0 }}min
            <span style="color:var(--muted);">(will be recalculated on save)</span>
        </div>
    </div>
</div>

<div style="margin-top:18px;display:flex;gap:10px;">
    <button type="submit" class="btn btn-accent"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
    <form action="{{ route('admin.attendance.destroy', $attendance) }}" method="POST" style="display:inline;">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger" data-confirm="Delete this attendance record permanently?">
            <i class="fa-solid fa-trash"></i> Delete
        </button>
    </form>
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline">Cancel</a>
</div>

</form>
</div>
@endsection
--}}