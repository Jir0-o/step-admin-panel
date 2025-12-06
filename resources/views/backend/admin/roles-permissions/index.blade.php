@extends('layouts.master')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid py-3">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Validation error:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- ROLES COLUMN --}}
        <div class="col-md-6 mb-3">
            <div class="card theme-shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Roles</h5>
                </div>
                <div class="card-body">

                    {{-- Create Role --}}
                    <form action="{{ route('admin.roles.store') }}" method="POST" class="row g-2 mb-3">
                        @csrf
                        <div class="col-8">
                            <input type="text" name="name" class="form-control" placeholder="New role name" required>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary w-100">Add Role</button>
                        </div>
                    </form>

                    {{-- Roles list --}}
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($roles as $index => $role)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{-- inline edit role name --}}
                                        <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="d-flex gap-1">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" class="form-control form-control-sm"
                                                   value="{{ $role->name }}" required>
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">Save</button>
                                        </form>
                                    </td>
                                    <td>
                                        {{-- show current permissions as small badges --}}
                                        @foreach($role->permissions as $perm)
                                            <span class="badge bg-secondary mb-1" title="ID: {{ $perm->id }}">{{ $perm->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-end">
                                        {{-- Manage permissions button (opens modal) --}}
                                        @php
                                            // Get permission IDs for this role - ensure they're integers
                                            $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                                            // Debug info (remove after testing)
                                            // echo "<!-- Role: {$role->name} -->";
                                            // echo "<!-- Permission IDs: " . implode(', ', $rolePermissionIds) . " -->";
                                        @endphp
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rolePermModal"
                                                data-role-id="{{ $role->id }}"
                                                data-role-name="{{ $role->name }}"
                                                data-role-permissions="{{ json_encode($rolePermissionIds) }}">
                                            Permissions
                                        </button>

                                        {{-- Delete role --}}
                                        <form action="{{ route('admin.roles.destroy', $role) }}"
                                              method="POST"
                                              class="d-inline-block"
                                              onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No roles created yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        {{-- PERMISSIONS COLUMN --}}
        <div class="col-md-6 mb-3">
            <div class="card theme-shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Permissions</h5>
                </div>
                <div class="card-body">

                    {{-- Create permission --}}
                    <form action="{{ route('admin.permissions.store') }}" method="POST" class="row g-2 mb-3">
                        @csrf
                        <div class="col-8">
                            <input type="text" name="name" class="form-control" placeholder="New permission name" required>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary w-100">Add Permission</button>
                        </div>
                    </form>

                    {{-- Permissions list --}}
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Permission</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($permissions as $index => $permission)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><small class="text-muted">{{ $permission->id }}</small></td>
                                    <td>
                                        <form action="{{ route('admin.permissions.update', $permission) }}"
                                              method="POST"
                                              class="d-flex gap-1">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name"
                                                   class="form-control form-control-sm"
                                                   value="{{ $permission->name }}" required>
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">Save</button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.permissions.destroy', $permission) }}"
                                              method="POST"
                                              class="d-inline-block"
                                              onsubmit="return confirm('Delete this permission?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No permissions created yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div> {{-- .row --}}

    {{-- Modal: assign permissions to role --}}
    <div class="modal fade" id="rolePermModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <form id="rolePermForm"
                    method="POST"
                    action="#">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Permissions for <span id="modalRoleName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info d-none" id="debugInfo"></div>
                        <p class="mb-2">Select permissions for this role:</p>
                        <div class="row">
                            @foreach($permissions as $perm)
                                <div class="col-md-4 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input perm-checkbox"
                                               type="checkbox"
                                               name="permissions[]"
                                               value="{{ $perm->id }}"
                                               id="perm_{{ $perm->id }}">
                                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                                            {{ $perm->name }} <small class="text-muted">(ID: {{ $perm->id }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            @if($permissions->isEmpty())
                                <div class="col-12 text-muted">
                                    No permissions found. Create some on the right side.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Permissions</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('rolePermModal');
    var modalRoleName = document.getElementById('modalRoleName');
    var form = document.getElementById('rolePermForm');
    var debugInfo = document.getElementById('debugInfo');

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    

    modal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        
        var roleId   = button.getAttribute('data-role-id');
        var roleName = button.getAttribute('data-role-name');
        var rolePermsJson = button.getAttribute('data-role-permissions') || '[]';
        
        
        // Set modal title
        modalRoleName.textContent = roleName;
        
        // Set form action URL
        form.action = "{{ route('admin.roles.permissions.sync', ':roleId') }}".replace(':roleId', roleId);
        
        // Clear all checkboxes first
        document.querySelectorAll('.perm-checkbox').forEach(function (cb) {
            cb.checked = false;
        });
        
        // Parse permission IDs from JSON
        try {
            var rolePermIds = JSON.parse(rolePermsJson);
            
            
            // Mark existing permissions
            var checkedCount = 0;
            document.querySelectorAll('.perm-checkbox').forEach(function (cb) {
                // Convert checkbox value to number for comparison
                var checkboxValue = parseInt(cb.value);
                
                // Check if this checkbox value exists in rolePermIds
                var isChecked = rolePermIds.some(function(id) {
                    return parseInt(id) === checkboxValue;
                });
                
                if (isChecked) {
                    cb.checked = true;
                    checkedCount++;
                    console.log('Checked checkbox with value:', cb.value, 'checkbox ID:', cb.id);
                }
            });
            
            
        } catch (e) {
            console.error('Error parsing permissions:', e);
            if (debugInfo) {
                debugInfo.classList.remove('d-none');
                debugInfo.innerHTML = `<strong>Error:</strong> ${e.message}<br>JSON: ${rolePermsJson}`;
            }
        }
    });
    
    // Hide debug info when modal closes
    modal.addEventListener('hidden.bs.modal', function () {
        if (debugInfo) {
            debugInfo.classList.add('d-none');
        }
    });
});
</script>
@endsection
