@extends('layouts.admin')
@section('title','Edit Payroll')
@section('breadcrumb','Edit Payroll')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Payroll</h1>
        <p>Update payroll for <strong>{{ $payroll->employee->name }}</strong> — {{ \Carbon\Carbon::parse($payroll->month)->format('F Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline">
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
    <form method="POST" action="{{ route('admin.payroll.update', $payroll) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-peso-sign" style="color:var(--accent);"></i> &nbsp;Payroll Details</span>
            </div>
            <div class="card-body">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Employee</label>
                    <input type="text" class="form-control" value="{{ $payroll->employee->name }} ({{ $payroll->employee->employee_id }})" readonly
                        style="background:#f5f5f5;color:var(--muted);cursor:not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Month</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($payroll->month)->format('F Y') }}" readonly
                        style="background:#f5f5f5;color:var(--muted);cursor:not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Basic Salary (₱) <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="basic_salary" step="0.01" min="0"
                        class="form-control @error('basic_salary') is-invalid @enderror"
                        value="{{ old('basic_salary', $payroll->basic_salary) }}" required
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                    @error('basic_salary')<div style="color:var(--danger);font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Overtime Pay (₱) <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="overtime_pay" step="0.01" min="0"
                        class="form-control @error('overtime_pay') is-invalid @enderror"
                        value="{{ old('overtime_pay', $payroll->overtime_pay) }}" required
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                    @error('overtime_pay')<div style="color:var(--danger);font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom:22px;">
                    <label class="form-label" style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Deductions (₱) <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="deductions" step="0.01" min="0"
                        class="form-control @error('deductions') is-invalid @enderror"
                        value="{{ old('deductions', $payroll->deductions) }}" required
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
                    @error('deductions')<div style="color:var(--danger);font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;" class="form-submit-row">
                    <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline">Cancel</a>
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
