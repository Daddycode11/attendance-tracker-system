@extends('layouts.admin')
@section('title','Edit Employee')
@section('breadcrumb','Edit Employee')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Employee</h1>
        <p>Update details for <strong>{{ $employee->name }}</strong> ({{ $employee->employee_id }}).</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="fa-solid fa-circle-exclamation"></i>
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

<form method="POST" action="{{ route('admin.employees.update', $employee) }}">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="form-grid">

        {{-- Employee Information --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-user" style="color:var(--accent);"></i> &nbsp;Employee Information</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Employee ID</label>
                    <input type="text" class="form-control" value="{{ $employee->employee_id }}" readonly
                        style="background:#f5f5f5;color:var(--muted);cursor:not-allowed;font-weight:700;letter-spacing:.03em;">
                    <div class="form-hint" style="font-size:.73rem;color:var(--muted);">Auto-generated — cannot be changed</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $employee->name) }}" required>
                    @error('name')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $employee->email) }}" placeholder="e.g. juan@example.com">
                    @error('email')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-hint" style="font-size:.73rem;color:var(--muted);">Used as default recipient when sending attendance reports</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Department <span style="color:var(--danger);">*</span></label>
                    <select name="department" class="form-control @error('department') is-invalid @enderror" required>
                        <option value="">— Select Department —</option>
                        @foreach($departments as $d)
                        <option value="{{ $d }}" {{ old('department', $employee->department) == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                    @error('department')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Position <span style="color:var(--danger);">*</span></label>
                    <select name="position" class="form-control @error('position') is-invalid @enderror" required>
                        <option value="">— Select Position —</option>
                        @foreach($positions as $p)
                        <option value="{{ $p }}" {{ old('position', $employee->position) == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    @error('position')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Basic Salary (₱) <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="basic_salary" step="0.01" min="0"
                        class="form-control @error('basic_salary') is-invalid @enderror"
                        value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                    @error('basic_salary')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Login Account --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-key" style="color:var(--accent);"></i> &nbsp;Login Account</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Username <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $employee->user?->username) }}" required>
                    @error('username')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Leave blank to keep current password">
                    @error('password')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Re-enter new password">
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="Employee" {{ old('role', $employee->user?->role) == 'Employee' ? 'selected' : '' }}>Employee</option>
                        <option value="Admin" {{ old('role', $employee->user?->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                @if($employee->user)
                <div style="margin-top:16px;padding:14px;background:#f0fdf4;border-radius:8px;font-size:.82rem;color:#166534;">
                    <i class="fa-solid fa-circle-check"></i> &nbsp;
                    This employee has an active login account. Leave password blank to keep the current one.
                </div>
                @else
                <div style="margin-top:16px;padding:14px;background:#fef3c7;border-radius:8px;font-size:.82rem;color:#92400e;">
                    <i class="fa-solid fa-triangle-exclamation"></i> &nbsp;
                    This employee doesn't have a login account yet. Fill in the fields above to create one.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="form-submit-row" style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;">
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-accent">
            <i class="fa-solid fa-floppy-disk"></i> Save Changes
        </button>
    </div>
</form>

<style>
    .form-grid { margin-bottom: 0; }
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 480px) {
        .form-submit-row { flex-direction: column; }
        .form-submit-row .btn { width: 100%; justify-content: center; }
    }
    .card-body { padding: 20px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px; color: var(--ink); }
    .form-control {
        width: 100%; padding: 10px 14px; border: 1.5px solid var(--border);
        border-radius: 8px; font-size: .88rem; font-family: inherit;
        background: var(--white); transition: border-color .2s;
    }
    .form-control:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(232,93,38,.1); }
    .form-control.is-invalid { border-color: var(--danger); }
    .form-error { color: var(--danger); font-size: .76rem; margin-top: 4px; }
    .alert-danger {
        background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
        padding: 14px 18px; margin-bottom: 20px; display: flex; gap: 12px;
        align-items: flex-start; color: #991b1b; font-size: .85rem;
    }
    .alert-danger i { margin-top: 2px; color: var(--danger); }
</style>
@endsection
