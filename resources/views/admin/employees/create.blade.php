@extends('layouts.admin')
@section('title','Add Employee')
@section('breadcrumb','Add Employee')

@section('content')
<div class="page-header">
    <div>
        <h1>Add Employee</h1>
        <p>Create a new employee record and login account.</p>
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

<form method="POST" action="{{ route('admin.employees.store') }}">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="form-grid">

        {{-- Employee Information --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-user" style="color:var(--accent);"></i> &nbsp;Employee Information</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Employee ID</label>
                    <input type="text" class="form-control" value="{{ $nextEmployeeId }}" readonly
                        style="background:#f5f5f5;color:var(--muted);cursor:not-allowed;font-weight:700;letter-spacing:.03em;">
                    <div class="form-hint" style="font-size:.73rem;color:var(--muted);">Auto-generated — cannot be changed</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="e.g. Juan Dela Cruz" required>
                    @error('name')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="e.g. juan@example.com">
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
                        <option value="{{ $d }}" {{ old('department') == $d ? 'selected' : '' }}>{{ $d }}</option>
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
                        <option value="{{ $p }}" {{ old('position') == $p ? 'selected' : '' }}>{{ $p }}</option>
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
                        value="{{ old('basic_salary', '0.00') }}" required>
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
                        value="{{ old('username') }}" placeholder="e.g. juan.delacruz" required>
                    @error('username')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password <span style="color:var(--danger);">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Minimum 8 characters" required>
                    @error('password')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password <span style="color:var(--danger);">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Re-enter password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="Employee" {{ old('role') == 'Employee' ? 'selected' : '' }}>Employee</option>
                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div style="margin-top:16px;padding:14px;background:#eff6ff;border-radius:8px;font-size:.82rem;color:#1e40af;">
                    <i class="fa-solid fa-circle-info"></i> &nbsp;
                    The employee will use the username and password above to log in to the system.
                </div>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="form-submit-row" style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;">
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-accent">
            <i class="fa-solid fa-user-plus"></i> Create Employee
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
