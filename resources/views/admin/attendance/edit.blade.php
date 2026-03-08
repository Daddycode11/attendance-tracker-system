@extends('layouts.admin')
@section('title','Edit Attendance')
@section('breadcrumb','Edit Attendance')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Attendance</h1>
        <p>Update attendance for <strong>{{ $attendance->employee->name }}</strong> — {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger" style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;color:#991b1b;font-size:.85rem;">
    <i class="fa-solid fa-circle-exclamation" style="margin-top:2px;color:var(--danger);"></i>
    <div>
        <strong>Please fix the following errors:</strong>
        <ul style="margin:6px 0 0 18px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div style="max-width:580px;" class="profile-container">
    <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-calendar-pen" style="color:var(--accent);"></i> &nbsp;Attendance Details</span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Employee</label>
                    <input type="text" class="form-control" value="{{ $attendance->employee->name }} ({{ $attendance->employee->employee_id }})" readonly
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;background:#f5f5f5;color:var(--muted);cursor:not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Date</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}" readonly
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;background:#f5f5f5;color:var(--muted);cursor:not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Status <span style="color:var(--danger);">*</span></label>
                    <select name="status" class="form-control" required
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                        @foreach(['Present','Late','Absent','Half-day','Incomplete'] as $s)
                        <option value="{{ $s }}" {{ old('status', $attendance->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="form-grid">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time In (AM)</label>
                        <input type="time" name="time_in_am" class="form-control"
                            value="{{ old('time_in_am', $attendance->time_in_am ? \Carbon\Carbon::parse($attendance->time_in_am)->format('H:i') : '') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time Out (Lunch)</label>
                        <input type="time" name="time_out_lunch" class="form-control"
                            value="{{ old('time_out_lunch', $attendance->time_out_lunch ? \Carbon\Carbon::parse($attendance->time_out_lunch)->format('H:i') : '') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="form-grid">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time In (PM)</label>
                        <input type="time" name="time_in_pm" class="form-control"
                            value="{{ old('time_in_pm', $attendance->time_in_pm ? \Carbon\Carbon::parse($attendance->time_in_pm)->format('H:i') : '') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time Out (Final)</label>
                        <input type="time" name="time_out_final" class="form-control"
                            value="{{ old('time_out_final', $attendance->time_out_final ? \Carbon\Carbon::parse($attendance->time_out_final)->format('H:i') : '') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:6px;flex-wrap:wrap;" class="form-submit-row">
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-accent">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    @media (max-width: 768px) {
        .profile-container { max-width: 100% !important; }
    }
    @media (max-width: 480px) {
        .profile-container { max-width: 100% !important; }
        .form-submit-row { flex-direction: column; }
        .form-submit-row .btn { width: 100%; justify-content: center; }
    }
</style>
@endsection