@extends('layouts.admin')
@section('title','Departments')
@section('breadcrumb','Departments')

@section('content')
<div class="page-header">
    <div>
        <h1>Department Management</h1>
        <p>Manage departments for the Tourism Office.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:380px 1fr;gap:20px;align-items:start;" class="dept-grid">

    {{-- Add Department --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus" style="color:var(--accent);"></i> &nbsp;Add Department</span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;font-size:.82rem;color:#991b1b;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('admin.departments.store') }}">
                @csrf
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Tourism Operations" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Optional description">
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;">
                    <i class="fa-solid fa-plus"></i> Add Department
                </button>
            </form>
        </div>
    </div>

    {{-- Departments List --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $departments->count() }} Department{{ $departments->count() != 1 ? 's' : '' }}</span>
        </div>
        <div class="table-wrap">
            @if($departments->count())
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
                    @foreach($departments as $dept)
                    <tr id="row-{{ $dept->id }}">
                        <td>
                            <span class="view-mode-{{ $dept->id }}" style="font-weight:600;">{{ $dept->name }}</span>
                            <form class="edit-mode-{{ $dept->id }}" style="display:none;" method="POST" action="{{ route('admin.departments.update', $dept) }}">
                                @csrf @method('PUT')
                                <input type="text" name="name" class="form-control" value="{{ $dept->name }}" style="padding:6px 10px;font-size:.82rem;" required>
                            </form>
                        </td>
                        <td>
                            <span class="view-mode-{{ $dept->id }}">{{ $dept->description ?? '—' }}</span>
                            <span class="edit-mode-{{ $dept->id }}" style="display:none;">
                                <input type="text" name="description" form="edit-form-{{ $dept->id }}" class="form-control" value="{{ $dept->description }}" style="padding:6px 10px;font-size:.82rem;">
                            </span>
                        </td>
                        <td>
                            {{ \App\Models\Employee::where('department', $dept->name)->count() }}
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button type="button" class="btn btn-sm btn-primary view-mode-{{ $dept->id }}" onclick="toggleEdit({{ $dept->id }})" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="submit" form="edit-form-{{ $dept->id }}" class="btn btn-sm btn-success edit-mode-{{ $dept->id }}" style="display:none;" title="Save">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline edit-mode-{{ $dept->id }}" style="display:none;" onclick="toggleEdit({{ $dept->id }})" title="Cancel">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete this department?" title="Delete">
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
                <i class="fa-solid fa-building"></i>
                <h3>No departments yet</h3>
                <p>Add your first department using the form.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Hidden forms for inline edit --}}
@foreach($departments as $dept)
<form id="edit-form-{{ $dept->id }}" method="POST" action="{{ route('admin.departments.update', $dept) }}" style="display:none;">
    @csrf @method('PUT')
</form>
@endforeach

<style>
    @media (max-width: 768px) {
        .dept-grid { grid-template-columns: 1fr !important; }
    }
</style>

<script>
function toggleEdit(id) {
    document.querySelectorAll('.view-mode-' + id).forEach(el => el.style.display = el.style.display === 'none' ? '' : 'none');
    document.querySelectorAll('.edit-mode-' + id).forEach(el => el.style.display = el.style.display === 'none' ? '' : 'none');
}
</script>
@endsection
