@extends('layouts.admin')
@section('title','Manual Attendance')
@section('breadcrumb','Manual Attendance')

@section('content')
<div class="page-header">
    <div>
        <h1>Manual Attendance Entry</h1>
        <p>Record attendance on behalf of an employee.</p>
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
    <form method="POST" action="{{ route('admin.attendance.store') }}">
        @csrf

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-calendar-plus" style="color:var(--accent);"></i> &nbsp;Attendance Details</span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Employee <span style="color:var(--danger);">*</span></label>
                    <select name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                        <option value="">— Select Employee —</option>
                        @foreach($employees as $e)
                        <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->name }} ({{ $e->employee_id }})
                        </option>
                        @endforeach
                    </select>
                    @error('employee_id')<div style="color:var(--danger);font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="form-grid">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Date <span style="color:var(--danger);">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', today()->toDateString()) }}" required
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                        @error('date')<div style="color:var(--danger);font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Status <span style="color:var(--danger);">*</span></label>
                        <select name="status" class="form-control" required
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                            @foreach(['Present','Late','Absent','Half-day','Incomplete'] as $s)
                            <option value="{{ $s }}" {{ old('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="form-grid">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time In (AM)</label>
                        <input type="time" name="time_in_am" class="form-control" value="{{ old('time_in_am') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time Out (Lunch)</label>
                        <input type="time" name="time_out_lunch" class="form-control" value="{{ old('time_out_lunch') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="form-grid">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time In (PM)</label>
                        <input type="time" name="time_in_pm" class="form-control" value="{{ old('time_in_pm') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Time Out (Final)</label>
                        <input type="time" name="time_out_final" class="form-control" value="{{ old('time_out_final') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;">
                    </div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:6px;flex-wrap:wrap;" class="form-submit-row">
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-accent">
                        <i class="fa-solid fa-plus"></i> Create Record
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