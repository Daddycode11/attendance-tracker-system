@extends('layouts.admin')
@section('title','Payroll')
@section('breadcrumb','Payroll')

@section('content')
<div class="page-header">
    <div>
        <h1>Payroll Management</h1>
        <p>Generate and manage monthly payroll records.</p>
    </div>
    <div class="page-header-actions">
        <form method="POST" action="{{ route('admin.payroll.generate') }}" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <input type="month" name="month" class="form-control" value="{{ $month }}" style="width:auto;">
            <button type="submit" class="btn btn-accent">
                <i class="fa-solid fa-calculator"></i> Generate Payroll
            </button>
        </form>
    </div>
</div>

{{-- Month Filter --}}
<form method="GET">
    <div class="filter-bar">
        <div class="form-group">
            <label>Month</label>
            <input type="month" name="month" class="form-control" value="{{ $month }}">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="margin-top:24px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline" style="margin-top:24px;margin-left:6px;">Clear</a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <span class="card-title">{{ $payrolls->total() }} Record{{ $payrolls->total() != 1 ? 's' : '' }}</span>
        <span style="font-size:.78rem;color:var(--muted);">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</span>
    </div>
    <div class="table-wrap">
        @if($payrolls->count())
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Days Present</th>
                    <th>Absent</th>
                    <th>Late (min)</th>
                    <th>OT (min)</th>
                    <th>Basic Salary</th>
                    <th>OT Pay</th>
                    <th>Deductions</th>
                    <th>Net Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payrolls as $payroll)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $payroll->employee->name }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">{{ $payroll->employee->employee_id }}</div>
                    </td>
                    <td>{{ $payroll->total_days_present }}</td>
                    <td>{{ $payroll->absent_days }}</td>
                    <td>
                        @if($payroll->total_late_minutes > 0)
                            <span style="color:#d97706;font-weight:600;">{{ $payroll->total_late_minutes }}</span>
                        @else — @endif
                    </td>
                    <td>
                        @if($payroll->total_overtime_minutes > 0)
                            <span style="color:#7c3aed;font-weight:600;">{{ $payroll->total_overtime_minutes }}</span>
                        @else — @endif
                    </td>
                    <td>₱{{ number_format($payroll->basic_salary, 2) }}</td>
                    <td style="color:#16a34a;font-weight:600;">₱{{ number_format($payroll->overtime_pay, 2) }}</td>
                    <td style="color:#dc2626;font-weight:600;">₱{{ number_format($payroll->deductions, 2) }}</td>
                    <td style="font-weight:700;">₱{{ number_format($payroll->net_salary, 2) }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.payroll.show', $payroll) }}" class="btn btn-sm btn-outline" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.payroll.edit', $payroll) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.payroll.destroy', $payroll) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    data-confirm="Delete this payroll record?"
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
            <i class="fa-solid fa-peso-sign"></i>
            <h3>No payroll records</h3>
            <p>Generate payroll for a month using the button above.</p>
        </div>
        @endif
    </div>
    @if($payrolls->hasPages())
    <div class="pagination">
        @if(!$payrolls->onFirstPage())
            <a href="{{ $payrolls->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
        @endif
        @foreach($payrolls->getUrlRange(max(1,$payrolls->currentPage()-2), min($payrolls->lastPage(),$payrolls->currentPage()+2)) as $p=>$u)
            <a href="{{ $u }}" class="page-link {{ $p==$payrolls->currentPage()?'active':'' }}">{{ $p }}</a>
        @endforeach
        @if($payrolls->hasMorePages())
            <a href="{{ $payrolls->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
        @endif
    </div>
    @endif
</div>
@endsection
