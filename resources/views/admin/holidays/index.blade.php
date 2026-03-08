@extends('layouts.admin')
@section('title','Holidays')
@section('breadcrumb','Holidays')

@section('content')
<div class="page-header">
    <div>
        <h1>Holiday Calendar</h1>
        <p>Manage holidays — these are excluded from absent/working-day calculations in payroll.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:380px 1fr;gap:20px;align-items:start;" class="hol-grid">

    {{-- Add Holiday --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus" style="color:var(--accent);"></i> &nbsp;Add Holiday</span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;font-size:.82rem;color:#991b1b;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('admin.holidays.store') }}">
                @csrf
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Holiday Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Araw ng Kagitingan" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Date <span style="color:var(--danger);">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Type <span style="color:var(--danger);">*</span></label>
                    <select name="type" class="form-control">
                        <option value="Regular" {{ old('type') == 'Regular' ? 'selected' : '' }}>Regular Holiday</option>
                        <option value="Special" {{ old('type') == 'Special' ? 'selected' : '' }}>Special Non-Working</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;">
                    <i class="fa-solid fa-plus"></i> Add Holiday
                </button>
            </form>
        </div>
    </div>

    {{-- Holiday List --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $holidays->count() }} Holiday{{ $holidays->count() != 1 ? 's' : '' }}</span>
        </div>
        <div class="table-wrap">
            @if($holidays->count())
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Holiday</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($holidays as $hol)
                    <tr>
                        <td style="white-space:nowrap;">
                            <i class="fa-solid fa-calendar-day" style="color:var(--accent);margin-right:4px;"></i>
                            {{ $hol->date->format('M d, Y') }}
                            <div style="font-size:.7rem;color:var(--muted);">{{ $hol->date->format('l') }}</div>
                        </td>
                        <td style="font-weight:600;">{{ $hol->name }}</td>
                        <td>
                            @if($hol->type === 'Regular')
                            <span class="badge" style="background:#dcfce7;color:#16a34a;">Regular</span>
                            @else
                            <span class="badge" style="background:#eff6ff;color:#2563eb;">Special</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button type="button" class="btn btn-sm btn-primary" title="Edit"
                                    onclick="openHolModal({{ $hol->id }}, '{{ addslashes($hol->name) }}', '{{ $hol->date->format('Y-m-d') }}', '{{ $hol->type }}')">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('admin.holidays.destroy', $hol) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this holiday?" title="Delete">
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
                <i class="fa-solid fa-umbrella-beach"></i>
                <h3>No holidays set</h3>
                <p>Add holidays so payroll can properly calculate working days.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="hol-modal-overlay" id="holEditModal" onclick="if(event.target===this)closeHolModal()">
    <div class="hol-modal">
        <div class="hol-modal-header">
            <h3><i class="fa-solid fa-pen" style="color:var(--accent);"></i> &nbsp;Edit Holiday</h3>
            <button class="hol-modal-close" onclick="closeHolModal()">&times;</button>
        </div>
        <div class="hol-modal-body">
            <form id="holEditForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Holiday Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="holEditName" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Date <span style="color:var(--danger);">*</span></label>
                    <input type="date" name="date" id="holEditDate" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Type <span style="color:var(--danger);">*</span></label>
                    <select name="type" id="holEditType" class="form-control">
                        <option value="Regular">Regular Holiday</option>
                        <option value="Special">Special Non-Working</option>
                    </select>
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline" onclick="closeHolModal()">Cancel</button>
                    <button type="submit" class="btn btn-accent"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) { .hol-grid { grid-template-columns: 1fr !important; } }
    .hol-modal-overlay { display:none; position:fixed; inset:0; z-index:999; background:rgba(0,0,0,.5); backdrop-filter:blur(2px); align-items:center; justify-content:center; padding:16px; }
    .hol-modal { background:#fff; border-radius:14px; width:100%; max-width:440px; box-shadow:0 20px 50px rgba(0,0,0,.2); overflow:hidden; }
    .hol-modal-header { padding:18px 22px; border-bottom:1px solid #f0ede6; display:flex; justify-content:space-between; align-items:center; }
    .hol-modal-header h3 { font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; margin:0; }
    .hol-modal-close { background:none; border:none; font-size:1.2rem; color:#999; cursor:pointer; padding:4px; border-radius:6px; }
    .hol-modal-close:hover { color:#333; background:#f3f4f6; }
    .hol-modal-body { padding:22px; }
    @media (max-width:480px) { .hol-modal { max-width:100% !important; } }
</style>

<script>
function openHolModal(id, name, date, type) {
    document.getElementById('holEditForm').action = '/admin/holidays/' + id;
    document.getElementById('holEditName').value = name;
    document.getElementById('holEditDate').value = date;
    document.getElementById('holEditType').value = type;
    document.getElementById('holEditModal').style.display = 'flex';
}
function closeHolModal() {
    document.getElementById('holEditModal').style.display = 'none';
}
</script>
@endsection
