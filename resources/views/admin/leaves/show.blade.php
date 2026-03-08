@extends('layouts.admin')
@section('title','Leave Details')
@section('breadcrumb','Leave Details')

@section('content')
<div class="page-header">
    <div>
        <h1>Leave Details</h1>
        <p>Viewing leave request from <strong>{{ $leave->employee->name }}</strong>.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.leaves.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
        <a href="{{ route('admin.leaves.edit', $leave) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
    </div>
</div>

<div style="max-width:580px;" class="profile-container">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-calendar-check" style="color:var(--accent);"></i> &nbsp;Leave Information</span>
            @php
                $bc = match($leave->status){
                    'Approved'=>'badge-present','Rejected'=>'badge-absent',default=>'badge-late'
                };
            @endphp
            <span class="badge {{ $bc }}">{{ $leave->status }}</span>
        </div>
        <div class="card-body" style="padding:20px;">
            <div class="payroll-row"><span>Employee</span><span style="font-weight:600;">{{ $leave->employee->name }}</span></div>
            <div class="payroll-row"><span>Employee ID</span><span>{{ $leave->employee->employee_id }}</span></div>
            <div class="payroll-row"><span>Leave Type</span><span style="font-weight:600;">{{ $leave->leave_type }}</span></div>
            <div class="payroll-row"><span>Start Date</span><span>{{ $leave->start_date->format('M d, Y') }}</span></div>
            <div class="payroll-row"><span>End Date</span><span>{{ $leave->end_date->format('M d, Y') }}</span></div>
            <div class="payroll-row"><span>Duration</span><span>{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} day(s)</span></div>
            <div class="payroll-row"><span>Reason</span><span>{{ $leave->reason ?? '—' }}</span></div>
            <div class="payroll-row"><span>Filed On</span><span>{{ $leave->created_at->format('M d, Y h:i A') }}</span></div>

            @if($leave->admin_remarks)
            <div class="payroll-row">
                <span>Admin Remarks</span>
                <span style="font-weight:600;color:{{ $leave->status === 'Approved' ? 'var(--success)' : ($leave->status === 'Rejected' ? 'var(--danger)' : 'var(--ink)') }};">{{ $leave->admin_remarks }}</span>
            </div>
            @endif

            @if($leave->status === 'Pending')
            <div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap;">
                <button type="button" class="btn btn-success" style="flex:1;justify-content:center;"
                    onclick="openLeaveModal('approve')">
                    <i class="fa-solid fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" style="flex:1;justify-content:center;"
                    onclick="openLeaveModal('reject')">
                    <i class="fa-solid fa-xmark"></i> Reject
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .payroll-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; border-bottom: 1px solid var(--border); font-size: .88rem;
    }
    .payroll-row:last-child { border-bottom: none; }

    @media (max-width: 768px) {
        .profile-container { max-width: 100% !important; }
    }
</style>

{{-- Approve / Reject Modal --}}
<div class="leave-modal-overlay" id="leaveActionModal" onclick="if(event.target===this)closeLeaveModal()">
    <div class="leave-modal">
        <div class="leave-modal-header">
            <h3 id="leaveModalTitle"><i class="fa-solid fa-check-circle" style="color:var(--success);"></i> &nbsp;Approve Leave</h3>
            <button class="leave-modal-close" onclick="closeLeaveModal()">&times;</button>
        </div>
        <div class="leave-modal-body">
            <p id="leaveModalDesc">Add an optional remark before approving this leave request.</p>
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
function openLeaveModal(action) {
    const modal   = document.getElementById('leaveActionModal');
    const form    = document.getElementById('leaveActionForm');
    const title   = document.getElementById('leaveModalTitle');
    const desc    = document.getElementById('leaveModalDesc');
    const btn     = document.getElementById('leaveModalBtn');
    const btnText = document.getElementById('leaveModalBtnText');

    if (action === 'approve') {
        form.action = '{{ route("admin.leaves.approve", $leave) }}';
        title.innerHTML = '<i class="fa-solid fa-check-circle" style="color:var(--success);"></i> &nbsp;Approve Leave';
        desc.textContent = 'Add an optional remark before approving this leave request.';
        btn.className = 'btn btn-success';
        btnText.textContent = 'Approve';
    } else {
        form.action = '{{ route("admin.leaves.reject", $leave) }}';
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
