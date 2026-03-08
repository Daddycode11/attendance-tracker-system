@extends('layouts.admin')
@section('title','Payroll Settings')
@section('breadcrumb','Payroll Settings')

@section('content')
<div class="page-header">
    <div>
        <h1>Payroll Settings</h1>
        <p>Configure working days, hours, overtime rates, and grace periods used in payroll calculations.</p>
    </div>
</div>

<div style="max-width:580px;" class="profile-container">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-gear" style="color:var(--accent);"></i> &nbsp;Calculation Settings</span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;font-size:.82rem;color:#991b1b;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.payroll.update') }}">
                @csrf @method('PUT')

                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label">Working Days per Month</label>
                    <input type="number" name="working_days_per_month" class="form-control"
                        value="{{ old('working_days_per_month', $settings->working_days_per_month) }}" min="1" max="31" required>
                    <div style="font-size:.73rem;color:var(--muted);margin-top:4px;">
                        Used to calculate daily rate: Basic Salary ÷ Working Days
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label">Working Hours per Day</label>
                    <input type="number" name="working_hours_per_day" class="form-control"
                        value="{{ old('working_hours_per_day', $settings->working_hours_per_day) }}" min="1" max="24" required>
                    <div style="font-size:.73rem;color:var(--muted);margin-top:4px;">
                        Used to calculate minute rate for late deductions (Working Hours × 60)
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label">Overtime Rate Multiplier</label>
                    <input type="number" name="ot_rate_multiplier" class="form-control" step="0.01"
                        value="{{ old('ot_rate_multiplier', $settings->ot_rate_multiplier) }}" min="1" max="5" required>
                    <div style="font-size:.73rem;color:var(--muted);margin-top:4px;">
                        OT Pay = Hourly Rate × OT Hours × Multiplier (e.g. 1.25 = 125% pay)
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label">Late Grace Period (minutes)</label>
                    <input type="number" name="late_grace_minutes" class="form-control"
                        value="{{ old('late_grace_minutes', $settings->late_grace_minutes) }}" min="0" max="60" required>
                    <div style="font-size:.73rem;color:var(--muted);margin-top:4px;">
                        Minutes after official start time before being counted as late (0 = no grace)
                    </div>
                </div>

                <div style="padding:14px;background:#eff6ff;border-radius:8px;font-size:.82rem;color:#1e40af;margin-bottom:18px;">
                    <i class="fa-solid fa-circle-info"></i> &nbsp;
                    Changes take effect the next time payroll is generated. Previously generated payrolls are not affected.
                </div>

                <button type="submit" class="btn btn-accent" style="width:100%;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Settings
                </button>
            </form>
        </div>
    </div>

    {{-- Current values summary --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-calculator" style="color:var(--accent);"></i> &nbsp;Calculation Preview</span>
        </div>
        <div class="card-body" style="padding:20px;">
            <div class="payroll-row"><span>Daily Rate Formula</span><span>Basic Salary ÷ {{ $settings->working_days_per_month }}</span></div>
            <div class="payroll-row"><span>Minute Rate</span><span>Daily Rate ÷ {{ $settings->working_hours_per_day * 60 }} min</span></div>
            <div class="payroll-row"><span>OT Hourly Rate</span><span>Hourly Rate × {{ $settings->ot_rate_multiplier }}</span></div>
            <div class="payroll-row"><span>Late Grace</span><span>{{ $settings->late_grace_minutes }} minute{{ $settings->late_grace_minutes != 1 ? 's' : '' }}</span></div>
        </div>
    </div>
</div>

<style>
    .payroll-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; border-bottom: 1px solid var(--border); font-size: .88rem;
    }
    .payroll-row:last-child { border-bottom: none; }
    @media (max-width: 768px) { .profile-container { max-width: 100% !important; } }
</style>
@endsection
