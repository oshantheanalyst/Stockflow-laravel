<div>
    {{-- Header --}}
    <div class="card mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-text-primary">Manage Users</h2>
                <p class="text-sm text-text-secondary">Add, edit and manage user accounts</p>
            </div>
            <div class="flex items-center gap-3">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search username..." class="form-input w-64" />
                <button wire:click="openAddModal" class="btn-primary cursor-pointer">+ Add User</button>
            </div>
        </div>
    </div>

    {{-- Session Message Alerts --}}
    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Data Table --}}
    <div class="card overflow-x-auto relative">
        <table class="data-table">
            <thead>
                <tr>
                    <th>USERNAME</th>
                    <th>ROLE</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    @php
                        $isSelf = auth()->id() === $u->id;
                    @endphp
                    <tr wire:key="user-{{ $u->id }}-{{ $u->is_active ? 'active' : 'inactive' }}">
                        <td class="font-medium text-slate-800">
                            {{ $u->username }}
                            @if($isSelf)
                                <span class="text-xs text-indigo-500 font-bold ml-1">(You)</span>
                            @endif
                        </td>
                        <td>
                            @if($u->role === 'Admin')
                                <span class="badge bg-purple-100 text-purple-700 font-semibold px-2 py-1 rounded-md text-[10px]">Admin</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-700 font-semibold px-2 py-1 rounded-md text-[10px]">User</span>
                            @endif
                        </td>
                        <td>
                            @if($u->is_active)
                                <span class="badge-success">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Active</span>
                                </span>
                            @else
                                <span class="badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap items-center gap-2">
                                <button wire:click="editUser({{ $u->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-100 transition-all cursor-pointer">
                                    Edit
                                </button>
                                <button wire:click="selectUserForReset({{ $u->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-100 transition-all cursor-pointer">
                                    Reset Pwd
                                </button>
                                @if(!$isSelf)
                                    <button wire:click="toggleActive({{ $u->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-xl {{ $u->is_active ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600' }} border border-slate-200 transition-all cursor-pointer">
                                        {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button wire:click="deleteUser({{ $u->id }})" onclick="confirm('Delete user account?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 transition-all cursor-pointer">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-text-secondary py-12">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4 px-4 py-2">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Add User Modal --}}
    <div class="modal-overlay {{ $showAddModal ? 'active' : '' }}">
        <div class="modal-content">
            <form wire:submit.prevent="createUser">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="text-lg font-bold">Add User</h3>
                    <button type="button" wire:click="resetFields" class="text-slate-400 hover:text-slate-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <label class="form-label">Username</label>
                        <input wire:model="username" class="form-input" required />
                        @error('username') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input type="password" wire:model="password" class="form-input" required />
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Role</label>
                        <select wire:model="role" class="form-select">
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                        @error('role') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-border flex justify-end gap-2">
                    <button type="button" wire:click="resetFields" class="btn-ghost cursor-pointer">Cancel</button>
                    <button type="submit" class="btn-primary cursor-pointer">Save User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div class="modal-overlay {{ $showEditModal ? 'active' : '' }}">
        <div class="modal-content">
            <form wire:submit.prevent="updateUser">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="text-lg font-bold">Edit User</h3>
                    <button type="button" wire:click="resetFields" class="text-slate-400 hover:text-slate-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <label class="form-label">Username</label>
                        <input wire:model="editingUsername" class="form-input" required />
                        @error('editingUsername') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Role</label>
                        <select wire:model="editingRole" class="form-select">
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                        @error('editingRole') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-border flex justify-end gap-2">
                    <button type="button" wire:click="resetFields" class="btn-ghost cursor-pointer">Cancel</button>
                    <button type="submit" class="btn-primary cursor-pointer">Update User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reset Password Modal --}}
    <div class="modal-overlay {{ $showResetModal ? 'active' : '' }}">
        <div class="modal-content">
            <form wire:submit.prevent="resetPassword">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="text-lg font-bold">Reset Password</h3>
                    <button type="button" wire:click="resetFields" class="text-slate-400 hover:text-slate-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <label class="form-label">New Password</label>
                        <input type="password" wire:model="newPassword" class="form-input" required />
                        @error('newPassword') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-border flex justify-end gap-2">
                    <button type="button" wire:click="resetFields" class="btn-ghost cursor-pointer">Cancel</button>
                    <button type="submit" class="btn-primary cursor-pointer">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
