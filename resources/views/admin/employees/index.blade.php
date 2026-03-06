{{-- ════════════════════════════════════════════════════════
     FILE: resources/views/admin/employees/index.blade.php
════════════════════════════════════════════════════════ --}}
@extends('layouts.admin')
@section('title','Employees')
@section('breadcrumb','Employees')

@section('content')
<div class="page-header">
    <div>
        <h1>Employees</h1>
        <p>Manage all employee records and accounts.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.employees.create') }}" class="btn btn-accent">
            <i class="fa-solid fa-user-plus"></i> Add Employee
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.employees.index') }}">
    <div class="filter-bar">
        <div class="form-group">
            <label>Search</label>
            <input type="text" name="search" class="form-control" placeholder="Name, ID, dept…" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label>Department</label>
            <select name="department" class="form-control">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                <option value="{{ $d }}" {{ request('department') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="margin-top:24px;">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline" style="margin-top:24px;margin-left:6px;">Clear</a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <span class="card-title">{{ $employees->total() }} Employee{{ $employees->total() != 1 ? 's' : '' }}</span>
    </div>
    <div class="table-wrap">
        @if($employees->count())
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Basic Salary</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;
                                background:linear-gradient(135deg,#667eea,#764ba2);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($emp->name,0,2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $emp->name }}</div>
                                <div style="font-size:.72rem;color:var(--muted);">{{ $emp->user?->username ?? 'No account' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><code style="font-size:.8rem;">{{ $emp->employee_id }}</code></td>
                    <td>{{ $emp->department ?? '—' }}</td>
                    <td>{{ $emp->position ?? '—' }}</td>
                    <td>₱{{ number_format($emp->basic_salary, 2) }}</td>
                    <td>
                        @if($emp->user)
                        <span class="badge {{ $emp->user->role === 'Admin' ? 'badge-admin' : 'badge-employee' }}">
                            {{ $emp->user->role }}
                        </span>
                        @else
                        <span class="badge badge-half">No Account</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.employees.show', $emp) }}" class="btn btn-sm btn-outline" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.employees.edit', $emp) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.employees.destroy', $emp) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    data-confirm="Delete {{ $emp->name }}? This will also remove their attendance and leave records."
                                    title="Delete">
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
            <i class="fa-solid fa-users-slash"></i>
            <h3>No employees found</h3>
            <p>Try adjusting your filters or add a new employee.</p>
        </div>
        @endif
    </div>
    {{-- Pagination --}}
    @if($employees->hasPages())
    <div class="pagination">
        @if($employees->onFirstPage())
            <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
        @else
            <a href="{{ $employees->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
        @endif

        @foreach($employees->getUrlRange(max(1, $employees->currentPage()-2), min($employees->lastPage(), $employees->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $page == $employees->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if($employees->hasMorePages())
            <a href="{{ $employees->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
        @else
            <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
        @endif
    </div>
    @endif
</div>
@endsection