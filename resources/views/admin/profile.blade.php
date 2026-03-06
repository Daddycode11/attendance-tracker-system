@extends('layouts.admin')

@section('title', 'My Profile')
@section('breadcrumb', 'My Profile')

@section('content')

<div class="page-header">
    <div>
        <h1>My Profile</h1>
        <p>Manage your account settings and change your password.</p>
    </div>
</div>

<div style="max-width: 580px;">

    {{-- ── Account Info Card ── --}}
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <span class="card-title">Account Information</span>
        </div>
        <div class="card-body">

            {{-- Avatar + name --}}
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:22px;">
                <div style="
                    width:64px; height:64px; border-radius:50%;
                    background: var(--accent);
                    display:flex; align-items:center; justify-content:center;
                    color:#fff; font-family:'Syne',sans-serif;
                    font-size:1.6rem; font-weight:800; flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->username, 0, 2)) }}
                </div>
                <div>
                    <div style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.1rem;">
                        {{ auth()->user()->employee?->name ?? auth()->user()->username }}
                    </div>
                    <div style="font-size:.82rem; color:var(--muted); margin-top:2px;">
                        @{{ auth()->user()->username }}
                    </div>
                    <span class="badge badge-admin" style="margin-top:6px; display:inline-block;">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>

            {{-- Employee details --}}
            @if(auth()->user()->employee)
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; font-size:.85rem;">
                <div>
                    <div style="color:var(--muted); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px;">Employee ID</div>
                    <div style="font-weight:600;">{{ auth()->user()->employee->employee_id }}</div>
                </div>
                <div>
                    <div style="color:var(--muted); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px;">Department</div>
                    <div style="font-weight:600;">{{ auth()->user()->employee->department ?? '—' }}</div>
                </div>
                <div>
                    <div style="color:var(--muted); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px;">Position</div>
                    <div style="font-weight:600;">{{ auth()->user()->employee->position ?? '—' }}</div>
                </div>
                <div>
                    <div style="color:var(--muted); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; margin-bottom:3px;">Basic Salary</div>
                    <div style="font-weight:600;">₱{{ number_format(auth()->user()->employee->basic_salary, 2) }}</div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Change Password Card ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-key" style="color:var(--accent);"></i> &nbsp;Change Password</span>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('admin.change-password') }}">
                @csrf

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Current Password</label>
                    <input
                        type="password"
                        name="current_password"
                        class="form-control @error('current_password') is-invalid @enderror"
                        placeholder="Enter current password"
                        required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">New Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Min. 8 characters"
                        required
                        minlength="8">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:22px;">
                    <label class="form-label">Confirm New Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Re-enter new password"
                        required>
                </div>

                <button type="submit" class="btn btn-accent">
                    <i class="fa-solid fa-key"></i> Change Password
                </button>
            </form>

        </div>
    </div>

</div>

@endsection