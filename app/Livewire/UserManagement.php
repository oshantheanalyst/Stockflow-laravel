<?php

namespace App\Livewire;

use App\Traits\DispatchesApiRequests;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;
    use DispatchesApiRequests;

    public $search = '';
    public $page = 1;

    public $username = '';
    public $password = '';
    public $role = 'User';

    public $editingUserId = null;
    public $editingUsername = '';
    public $editingRole = 'User';

    public $resetUserId = null;
    public $newPassword = '';

    public $showAddModal = false;
    public $showEditModal = false;
    public $showResetModal = false;

    public $userList = [];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->username = '';
        $this->password = '';
        $this->role = 'User';

        $this->editingUserId = null;
        $this->editingUsername = '';
        $this->editingRole = 'User';

        $this->resetUserId = null;
        $this->newPassword = '';

        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->showResetModal = false;
    }

    public function openAddModal()
    {
        $this->resetFields();
        $this->showAddModal = true;
    }

    public function createUser()
    {
        $this->validate([
            'username' => 'required|min:3',
            'password' => 'required|min:6',
            'role'     => 'required',
        ]);

        $response = $this->apiPost('/users', [
            'username' => $this->username,
            'password' => $this->password,
            'role'     => $this->role,
        ]);

        if (! $response->ok || ! ($response->payload['success'] ?? false)) {
            session()->flash('error', $response->payload['message'] ?? 'Unable to create user.');
            return;
        }

        $this->resetFields();
        session()->flash('message', $response->payload['message'] ?? 'User created successfully.');
    }

    public function editUser($userId)
    {
        $response = $this->apiGet('/users/' . $userId);

        if (! $response->ok || ! isset($response->payload['data'])) {
            session()->flash('error', 'Unable to load user data.');
            return;
        }

        $user = $response->payload['data'];
        $this->editingUserId = $user['id'];
        $this->editingUsername = $user['username'];
        $this->editingRole = $user['role'];
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'editingUsername' => 'required|min:3',
            'editingRole'     => 'required',
        ]);

        $response = $this->apiPut('/users/' . $this->editingUserId, [
            'username' => $this->editingUsername,
            'role'     => $this->editingRole,
        ]);

        if (! $response->ok || ! ($response->payload['success'] ?? false)) {
            session()->flash('error', $response->payload['message'] ?? 'Unable to update user.');
            return;
        }

        $this->resetFields();
        session()->flash('message', $response->payload['message'] ?? 'User updated successfully.');
    }

    public function selectUserForReset($userId)
    {
        $this->resetUserId = $userId;
        $this->newPassword = '';
        $this->showResetModal = true;
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => 'required|min:6',
        ]);

        $response = $this->apiPut('/users/' . $this->resetUserId . '/reset-password', [
            'password' => $this->newPassword,
        ]);

        if (! $response->ok || ! ($response->payload['success'] ?? false)) {
            session()->flash('error', $response->payload['message'] ?? 'Unable to reset password.');
            return;
        }

        $this->resetFields();
        session()->flash('message', $response->payload['message'] ?? 'Password updated successfully.');
    }

    public function toggleActive($userId)
    {
        if ($userId === \Illuminate\Support\Facades\Auth::id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        try {
            $user = \App\Models\User::findOrFail($userId);
            $newStatus = !$user->is_active;

            $response = $this->apiPut('/users/' . $userId, [
                'is_active' => $newStatus,
            ]);

            if ($response->ok && ($response->payload['success'] ?? false)) {
                session()->flash('message', 'User status updated successfully.');
            } else {
                session()->flash('error', $response->payload['message'] ?? 'Unable to update user status.');
            }
        } catch (\Exception $e) {
            \Log::error('toggleActive failed: ' . $e->getMessage());
            session()->flash('error', 'Unable to update user status.');
        }
    }

    public function deleteUser($userId)
    {
        if ($userId === \Illuminate\Support\Facades\Auth::id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        try {
            $user = \App\Models\User::findOrFail($userId);
            $user->tokens()->delete();
            $user->delete();
            $this->resetFields();
            session()->flash('message', 'User account deleted.');
        } catch (\Exception $e) {
            \Log::error('deleteUser failed: ' . $e->getMessage());
            session()->flash('error', 'Unable to delete user.');
        }
    }

    public function findUserById($userId)
    {
        return collect($this->userList)->firstWhere('id', $userId) ?: (object) ['is_active' => true];
    }

    public function render()
    {
        $response = $this->apiGet('/users', ['search' => $this->search]);

        $users = collect($response->payload['data'] ?? [])->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });

        $this->userList = $users->all();

        $perPage = 10;
        $currentPage = max(1, $this->page ?? 1);
        $pagedUsers = $users->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedUsers,
            $users->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        return view('livewire.user-management', [
            'users' => $paginated,
        ]);
    }
}
