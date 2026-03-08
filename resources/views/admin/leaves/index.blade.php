@extends('layouts.admin')
@section('title','Leaves')
@section('breadcrumb','Leaves')

@section('content')
<div class="page-header">
    <div>
        <h1>Leave Management</h1>
        <p>Review and manage employee leave requests.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.leaves.create') }}" class="btn btn-accent">
            <i class="fa-solid fa-plus"></i> Add Leave
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET">
    <div class="filter-bar">
        <div class="form-group">
            <label>Employee</label>
            <select name="employee_id" class="form-control">
                <option value="">All Employees</option>
                @foreach($employees as $e)
                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">All Status</option>
                @foreach(['Pending','Approved','Rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="margin-top:24px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline" style="margin-top:24px;margin-left:6px;">Clear</a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <span class="card-title">{{ $leaves->total() }} Leave{{ $leaves->total() != 1 ? 's' : '' }}</span>
    </div>
    <div class="table-wrap">
        @if($leaves->count())
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaves as $leave)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $leave->employee->name }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">{{ $leave->employee->employee_id }}</div>
                    </td>
                    <td>{{ $leave->leave_type }}</td>
                    <td style="white-space:nowrap;">{{ $leave->start_date->format('M d, Y') }}</td>
                    <td style="white-space:nowrap;">{{ $leave->end_date->format('M d, Y') }}</td>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $leave->reason ?? '—' }}</td>
                    <td>
                        @php
                            $bc = match($leave->status){
                                'Approved'=>'badge-present','Rejected'=>'badge-absent',default=>'badge-late'
                            };
                        @endphp
                        <span class="badge {{ $bc }}">{{ $leave->status }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @if($leave->status === 'Pending')
                            <button type="button" class="btn btn-sm btn-success" title="Approve"
                                onclick="openLeaveModal('approve', {{ $leave->id }})">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" title="Reject"
                                onclick="openLeaveModal('reject', {{ $leave->id }})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            @endif
                            
                            <form action="{{ route('admin.leaves.destroy', $leave) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    data-confirm="Delete this leave record?"
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
            <i class="fa-solid fa-calendar-xmark"></i>
            <h3>No leave records</h3>
            <p>No leave requests found. Try adjusting your filters.</p>
        </div>
        @endif
    </div>
    @if($leaves->hasPages())
    <div class="pagination">
        @if(!$leaves->onFirstPage())
            <a href="{{ $leaves->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
        @endif
        @foreach($leaves->getUrlRange(max(1,$leaves->currentPage()-2), min($leaves->lastPage(),$leaves->currentPage()+2)) as $p=>$u)
            <a href="{{ $u }}" class="page-link {{ $p==$leaves->currentPage()?'active':'' }}">{{ $p }}</a>
        @endforeach
        @if($leaves->hasMorePages())
            <a href="{{ $leaves->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
        @endif
    </div>
    @endif
</div>

{{-- Approve / Reject Modal --}}
<div class="leave-modal-overlay" id="leaveActionModal" onclick="if(event.target===this)closeLeaveModal()">
    <div class="leave-modal">
        <div class="leave-modal-header">
            <h3 id="leaveModalTitle"><i class="fa-solid fa-check-circle" style="color:var(--accent);"></i> &nbsp;Approve Leave</h3>
            <button class="leave-modal-close" onclick="closeLeaveModal()">&times;</button>
        </div>
        <div class="leave-modal-body">
            <p id="leaveModalDesc">Add an optional remark for this action.</p>
            <form id="leaveActionForm" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Admin Remarks <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                    <textarea name="admin_remarks" class="form-control" rows="3" placeholder="e.g. Approved, enjoy your leave..."></textarea>
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline" onclick="closeLeaveModal()">Cancel</button>
                    <button type="submit" class="btn" id="leaveModalBtn">
                        <i class="fa-solid fa-check"></i> <span id="leaveModalBtnText">Approve</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .leave-modal-overlay {
        display:none; position:fixed; inset:0; z-index:999;
        background:rgba(0,0,0,.5); backdrop-filter:blur(2px);
        align-items:center; justify-content:center; padding:16px;
    }
    .leave-modal {
        background:#fff; border-radius:14px; width:100%; max-width:440px;
        box-shadow:0 20px 50px rgba(0,0,0,.2); overflow:hidden;
    }
    .leave-modal-header {
        padding:18px 22px; border-bottom:1px solid #f0ede6;
        display:flex; justify-content:space-between; align-items:center;
    }
    .leave-modal-header h3 { font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; margin:0; }
    .leave-modal-close {
        background:none; border:none; font-size:1.2rem; color:#999;
        cursor:pointer; padding:4px; border-radius:6px; transition:all .2s;
    }
    .leave-modal-close:hover { color:#333; background:#f3f4f6; }
    .leave-modal-body { padding:22px; }
    .leave-modal-body p { font-size:.84rem; color:#6b7280; margin-bottom:16px; }
    @media (max-width:480px) {
        .leave-modal { max-width:100% !important; border-radius:10px !important; }
        .leave-modal-body { padding:16px !important; }
        .leave-modal-header { padding:14px 16px !important; }
    }
</style>

<script>
function openLeaveModal(action, leaveId) {
    const modal   = document.getElementById('leaveActionModal');
    const form    = document.getElementById('leaveActionForm');
    const title   = document.getElementById('leaveModalTitle');
    const desc    = document.getElementById('leaveModalDesc');
    const btn     = document.getElementById('leaveModalBtn');
    const btnText = document.getElementById('leaveModalBtnText');

    if (action === 'approve') {
        form.action = '/admin/leaves/' + leaveId + '/approve';
        title.innerHTML = '<i class="fa-solid fa-check-circle" style="color:var(--success);"></i> &nbsp;Approve Leave';
        desc.textContent = 'Add an optional remark before approving this leave request.';
        btn.className = 'btn btn-success';
        btnText.textContent = 'Approve';
    } else {
        form.action = '/admin/leaves/' + leaveId + '/reject';
        title.innerHTML = '<i class="fa-solid fa-circle-xmark" style="color:var(--danger);"></i> &nbsp;Reject Leave';
        desc.textContent = 'Add an optional remark before rejecting this leave request.';
        btn.className = 'btn btn-danger';
        btnText.textContent = 'Reject';
    }

    form.querySelector('textarea[name="admin_remarks"]').value = '';
    modal.style.display = 'flex';
}

function closeLeaveModal() {
    document.getElementById('leaveActionModal').style.display = 'none';
}
</script>

@endsection