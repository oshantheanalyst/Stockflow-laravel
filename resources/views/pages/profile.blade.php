@extends('layouts.app')
@section('title', 'My Profile')
@section('content')

<div class="card max-w-lg mx-auto bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm">
    <h2 class="text-2xl font-extrabold tracking-tight text-slate-800 mb-6">My Profile</h2>

    <div class="flex items-center gap-4 mb-6">
        <img id="profile-page-photo" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-primary/20 bg-slate-100 shadow-sm" src="" alt="Profile" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\'%3E%3Ccircle cx=\'12\' cy=\'8\' r=\'4\' fill=\'%2394A3B8\'/%3E%3Cpath d=\'M4 20c0-4 3.6-7 8-7s8 3 8 7\' fill=\'%2394A3B8\'/%3E%3C/svg%3E'">
        <div>
            <p id="profile-page-username" class="text-xl font-extrabold text-slate-800">...</p>
            <span id="profile-page-role" class="inline-flex items-center px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-md border border-indigo-100 mt-1">User</span>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <h3 class="text-base font-bold text-slate-700 mb-4">Change Password</h3>
        
        @if (session('status') == 'password-updated')
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl p-3.5 text-sm font-semibold mb-4">
                Password updated successfully.
            </div>
        @endif

        @if ($errors->updatePassword->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-3.5 text-sm font-semibold mb-4">
                {{ $errors->updatePassword->first() }}
            </div>
        @endif

        <form action="{{ route('user-password.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="form-label" style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:8px;" for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-input" style="width:100%; height:48px; padding:0 16px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; font-size:14px; color:#1E293B; outline:none; transition:all 0.2s;" required placeholder="Enter current password" />
            </div>
            
            <div>
                <label class="form-label" style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:8px;" for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-input" style="width:100%; height:48px; padding:0 16px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; font-size:14px; color:#1E293B; outline:none; transition:all 0.2s;" required placeholder="Enter new password" />
            </div>
            
            <div>
                <label class="form-label" style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:8px;" for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" style="width:100%; height:48px; padding:0 16px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; font-size:14px; color:#1E293B; outline:none; transition:all 0.2s;" required placeholder="Confirm new password" />
            </div>
            
            <button type="submit" class="btn-primary w-full mt-2" style="width:100%; height:48px; background:#2563EB; border:none; border-radius:10px; color:white; font-size:14px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s;">Update Password</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const user = getCurrentUser();
        if (user) {
            const photoEl = document.getElementById('profile-page-photo');
            const usernameEl = document.getElementById('profile-page-username');
            const roleEl = document.getElementById('profile-page-role');
            
            if (photoEl && user.profile_photo_url) photoEl.src = user.profile_photo_url;
            if (usernameEl) usernameEl.textContent = user.username;
            if (roleEl) roleEl.textContent = user.role || 'User';
        }
    });
</script>
@endpush
