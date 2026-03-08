@extends('layouts.admin')
@section('title','Positions')
@section('breadcrumb','Positions')

@section('content')
<div class="page-header">
    <div>
        <h1>Position Management</h1>
        <p>Manage job positions / designations.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:380px 1fr;gap:20px;align-items:start;" class="pos-grid">

    {{-- Add Position --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus" style="color:var(--accent);"></i> &nbsp;Add Position</span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;font-size:.82rem;color:#991b1b;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('admin.positions.store') }}">
                @csrf
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Tourism Officer I" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Optional description">
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;">
                    <i class="fa-solid fa-plus"></i> Add Position
                </button>
            </form>
        </div>
    </div>

    {{-- Positions List --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $positions->count() }} Position{{ $positions->count() != 1 ? 's' : '' }}</span>
        </div>
        <div class="table-wrap">
            @if($positions->count())
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Employees</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $pos)
                    <tr>
                        <td style="font-weight:600;">{{ $pos->name }}</td>
                        <td>{{ $pos->description ?? '—' }}</td>
                        <td>{{ \App\Models\Employee::where('position', $pos->name)->count() }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button type="button" class="btn btn-sm btn-primary" title="Edit"
                                    onclick="openEditModal({{ $pos->id }}, '{{ addslashes($pos->name) }}', '{{ addslashes($pos->description) }}')">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('admin.positions.destroy', $pos) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this position?" title="Delete">
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
                <i class="fa-solid fa-briefcase"></i>
                <h3>No positions yet</h3>
                <p>Add your first position using the form.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="pos-modal-overlay" id="editModal" onclick="if(event.target===this)closeEditModal()">
    <div class="pos-modal">
        <div class="pos-modal-header">
            <h3><i class="fa-solid fa-pen" style="color:var(--accent);"></i> &nbsp;Edit Position</h3>
            <button class="pos-modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="pos-modal-body">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" id="editDesc" class="form-control">
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-accent"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) { .pos-grid { grid-template-columns: 1fr !important; } }
    .pos-modal-overlay { display:none; position:fixed; inset:0; z-index:999; background:rgba(0,0,0,.5); backdrop-filter:blur(2px); align-items:center; justify-content:center; padding:16px; }
    .pos-modal { background:#fff; border-radius:14px; width:100%; max-width:440px; box-shadow:0 20px 50px rgba(0,0,0,.2); overflow:hidden; }
    .pos-modal-header { padding:18px 22px; border-bottom:1px solid #f0ede6; display:flex; justify-content:space-between; align-items:center; }
    .pos-modal-header h3 { font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; margin:0; }
    .pos-modal-close { background:none; border:none; font-size:1.2rem; color:#999; cursor:pointer; padding:4px; border-radius:6px; }
    .pos-modal-close:hover { color:#333; background:#f3f4f6; }
    .pos-modal-body { padding:22px; }
    @media (max-width:480px) { .pos-modal { max-width:100% !important; } }
</style>

<script>
function openEditModal(id, name, desc) {
    document.getElementById('editForm').action = '/admin/positions/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editDesc').value = desc || '';
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
@endsection
