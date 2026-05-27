@extends('layouts.app')
@section('title', 'Manage Users')
@section('content')
<div class="card mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-text-primary">Manage Users</h2><p class="text-sm text-text-secondary">Add, edit and manage user accounts</p></div>
        <div><button onclick="openModal('addUserModal')" class="btn-primary">+ Add User</button></div>
    </div>
</div>
<div class="card overflow-x-auto">
    <table class="data-table">
        <thead><tr><th>USERNAME</th><th>ROLE</th><th>STATUS</th><th>ACTIONS</th></tr></thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td class="font-medium">{{ $u->username }}</td>
                <td><span class="badge {{ $u->role === 'Admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">{{ $u->role }}</span></td>
                <td><span class="{{ $u->is_active ? 'badge-success' : 'badge-danger' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td class="flex flex-wrap items-center gap-2">
                    <button onclick="openEditUser({{ $u->id }}, '{{ addslashes($u->username) }}', '{{ $u->role }}')" 
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-indigo-50/80 hover:bg-indigo-100/80 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20 border border-indigo-100/50 hover:border-indigo-200 dark:border-indigo-500/20 dark:hover:border-indigo-500/30 transition-all duration-200 active:scale-95 shadow-sm cursor-pointer"
                            title="Edit User">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span>Edit</span>
                    </button>
                    <button onclick="openResetPassword({{ $u->id }})" 
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-amber-50/80 hover:bg-amber-100/80 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20 border border-amber-100/50 hover:border-amber-200 dark:border-amber-500/20 dark:hover:border-amber-500/30 transition-all duration-200 active:scale-95 shadow-sm cursor-pointer"
                            title="Reset Password">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 11a3 3 0 11-6 0 3 3 0 016 0zM12 10.5h9v3h-3v3h-3v-3h-3M9 12l2.2-2.2"/>
                        </svg>
                        <span>Reset Pwd</span>
                    </button>
                    @if(auth()->id() !== $u->id)
                    <form method="POST" action="{{ route('users.toggleActive', $u->id) }}" class="inline">
                        @csrf
                        @method('PUT')
                        @if($u->is_active)
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-100/80 hover:bg-slate-200/80 text-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-all duration-200 active:scale-95 shadow-sm cursor-pointer"
                                title="Deactivate User">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <span>Deactivate</span>
                        </button>
                        @else
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-emerald-50 hover:bg-emerald-100/80 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20 border border-emerald-100/50 hover:border-emerald-200 dark:border-emerald-500/20 dark:hover:border-emerald-500/30 transition-all duration-200 active:scale-95 shadow-sm cursor-pointer"
                                title="Activate User">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Activate</span>
                        </button>
                        @endif
                    </form>
                    <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-rose-50 hover:bg-rose-100/80 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20 border border-rose-100/50 hover:border-rose-200 dark:border-rose-500/20 dark:hover:border-rose-500/30 transition-all duration-200 active:scale-95 shadow-sm cursor-pointer"
                                title="Delete User">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Delete</span>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Add Modal --}}
<div id="addUserModal" class="modal-overlay" onclick="if(event.target===this)closeModal('addUserModal')">
    <div class="modal-content">
        <form method="POST" action="{{ route('users.store') }}">@csrf
            <div class="px-6 py-4 border-b border-border"><h3 class="text-lg font-bold">Add User</h3></div>
            <div class="px-6 py-4 space-y-3">
                <div><label class="form-label">Username</label><input name="username" class="form-input" required /></div>
                <div><label class="form-label">Password</label><input type="password" name="password" class="form-input" required /></div>
                <div><label class="form-label">Role</label><select name="role" class="form-select"><option>User</option><option>Admin</option></select></div>
            </div>
            <div class="px-6 py-4 border-t border-border flex justify-end gap-2"><button type="button" onclick="closeModal('addUserModal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Save</button></div>
        </form>
    </div>
</div>

{{-- Reset Pwd Modal --}}
<div id="resetPwdModal" class="modal-overlay" onclick="if(event.target===this)closeModal('resetPwdModal')">
    <div class="modal-content">
        <form method="POST" id="resetPwdForm">@csrf @method('PUT')
            <div class="px-6 py-4 border-b border-border"><h3 class="text-lg font-bold">Reset Password</h3></div>
            <div class="px-6 py-4">
                <div><label class="form-label">New Password</label><input type="password" name="password" class="form-input" required /></div>
            </div>
            <div class="px-6 py-4 border-t border-border flex justify-end gap-2"><button type="button" onclick="closeModal('resetPwdModal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Update</button></div>
        </form>
    </div>
</div>
{{-- Edit Modal --}}
<div id="editUserModal" class="modal-overlay" onclick="if(event.target===this)closeModal('editUserModal')">
    <div class="modal-content">
        <form method="POST" id="editUserForm">@csrf @method('PUT')
            <div class="px-6 py-4 border-b border-border"><h3 class="text-lg font-bold">Edit User</h3></div>
            <div class="px-6 py-4 space-y-3">
                <div><label class="form-label">Username</label><input name="username" id="edit_username" class="form-input" required /></div>
                <div><label class="form-label">Role</label><select name="role" id="edit_role" class="form-select"><option>User</option><option>Admin</option></select></div>
            </div>
            <div class="px-6 py-4 border-t border-border flex justify-end gap-2"><button type="button" onclick="closeModal('editUserModal')" class="btn-ghost">Cancel</button><button type="submit" class="btn-primary">Update</button></div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openEditUser(id, username, role) {
    document.getElementById('editUserForm').action = '{{ url('/users') }}/' + id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value = role;
    openModal('editUserModal');
}

function openResetPassword(id) {
    document.getElementById('resetPwdForm').action = '{{ url('/users') }}/' + id + '/reset-password';
    openModal('resetPwdModal');
}
</script>
@endpush
