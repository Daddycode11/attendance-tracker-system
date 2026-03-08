@extends('layouts.admin')
@section('title','Add Leave')
@section('breadcrumb','Add Leave')

@section('content')
<div class="page-header">
    <div>
        <h1>Add Leave</h1>
        <p>Create a leave record on behalf of an employee.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline">
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
    <form method="POST" action="{{ route('admin.leaves.store') }}">
        @csrf

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-calendar-plus" style="color:var(--accent);"></i> &nbsp;Leave Details</span>
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

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Leave Type <span style="color:var(--danger);">*</span></label>
                    <select name="leave_type" class="form-control" required
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                        @foreach(['Sick','Vacation','Others'] as $t)
                        <option value="{{ $t }}" {{ old('leave_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="form-grid">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Start Date <span style="color:var(--danger);">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                            value="{{ old('start_date') }}" required
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                        @error('start_date')<div style="color:var(--danger);font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">End Date <span style="color:var(--danger);">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date') }}" required
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                        @error('end_date')<div style="color:var(--danger);font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Reason</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Optional reason…"
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;resize:vertical;">{{ old('reason') }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom:22px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Status <span style="color:var(--danger);">*</span></label>
                    <select name="status" class="form-control" required
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                        <option value="{{ $s }}" {{ old('status', 'Pending') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;" class="form-submit-row">
                    <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-accent">
                        <i class="fa-solid fa-plus"></i> Create Leave
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
