<x-app-layout>
    @section('title', 'My Account')
    
    <!-- Account Settings Main Wrapper (Flexbox Powered) -->
    <div class="account-settings-container" x-data="{ activeTab: 'profile' }" style="max-width: 1200px; margin: 0 auto; padding: 25px; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box;">
        
        <!-- Header Panel -->
        <div class="profile-header-panel">
            <h1 class="profile-header-title">Account Settings</h1>
            <p class="profile-header-subtitle">Manage your profile details, password security, active sessions, and account state.</p>
        </div>

        <!-- Layout Wrapper -->
        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            
            <!-- Left Column: Profile Card + Navigation Menu (Width 320px) -->
            <div class="account-sidebar" style="width: 320px; display: flex; flex-direction: column; gap: 24px; flex-shrink: 0;">
                
                <!-- Premium Profile Card -->
                <div style="background: linear-gradient(135deg, #1e1b4b, #311042); color: white; border-radius: 24px; padding: 30px 25px; text-align: center; box-shadow: 0 15px 35px rgba(0,0,0,0.06); border: 1px solid rgba(255,255,255,0.05); position: relative; overflow: hidden; box-sizing: border-box;">
                    <div style="position: absolute; right: -25px; top: -25px; width: 120px; height: 120px; background: rgba(99, 102, 241, 0.15); border-radius: 50%; filter: blur(35px); pointer-events: none;"></div>
                    
                    <!-- Avatar circle with active green dot -->
                    <div style="position: relative; width: 96px; height: 96px; margin: 0 auto 18px auto;">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->username }}" style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 8px 16px rgba(0,0,0,0.25);">
                        <span style="position: absolute; bottom: 3px; right: 3px; width: 14px; height: 14px; background: #10b981; border-radius: 50%; border: 3px solid #1e1b4b; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></span>
                    </div>
                    
                    <h2 style="font-size: 1.2rem; font-weight: 700; margin: 0 0 4px 0; color: white;">{{ auth()->user()->name ?? auth()->user()->username }}</h2>
                    <p style="font-size: 0.75rem; color: #a5b4fc; margin: 0; font-weight: 500; opacity: 0.85; word-break: break-all;">{{ auth()->user()->email }}</p>
                    
                    <!-- Meta Grid (Stacked cleanly for alignment) -->
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); text-align: left; display: flex; flex-direction: column; gap: 14px; box-sizing: border-box;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #818cf8; font-weight: 700; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.05em;">Username</span>
                            <span style="font-weight: 700; color: white; font-size: 0.8rem;">{{ auth()->user()->username }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #818cf8; font-weight: 700; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.05em;">Member Since</span>
                            <span style="font-weight: 700; color: white; font-size: 0.8rem;">{{ auth()->user()->created_at ? auth()->user()->created_at->format('M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Sidebar Menu (Replaced buttons with div links to bypass global button overrides) -->
                <div class="account-sidebar-menu">
                    <p style="color: #94a3b8; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; margin: 5px 0 10px 10px;">Settings Menu</p>
                    
                    <!-- Profile Tab -->
                    <div @click="activeTab = 'profile'" 
                         :class="{ 'active': activeTab === 'profile' }"
                         class="account-menu-item">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Profile Information</span>
                    </div>

                    <!-- Security Tab -->
                    <div @click="activeTab = 'security'" 
                         :class="{ 'active': activeTab === 'security' }"
                         class="account-menu-item">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Security & Password</span>
                    </div>

                    <!-- Sessions Tab -->
                    <div @click="activeTab = 'sessions'" 
                         :class="{ 'active': activeTab === 'sessions' }"
                         class="account-menu-item">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Active Sessions</span>
                    </div>

                    <!-- Danger Tab -->
                    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                    <div @click="activeTab = 'danger'" 
                         :class="{ 'active': activeTab === 'danger' }"
                         class="account-menu-item danger-item">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Delete Account</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Settings Forms Content Panels (Flex Grow) -->
            <div class="account-content" style="flex: 1; min-width: 320px;">
                <div class="profile-forms-wrapper">
                    
                    <!-- Profile Information Tab Content -->
                    <div x-show="activeTab === 'profile'" x-transition>
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('profile.update-profile-information-form')
                        @endif
                    </div>

                    <!-- Security & Credentials Tab Content -->
                    <div x-show="activeTab === 'security'" x-transition style="display: flex; flex-direction: column; gap: 30px;">
                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                            @livewire('profile.update-password-form')
                        @endif

                        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                            @livewire('profile.two-factor-authentication-form')
                        @endif
                    </div>

                    <!-- Sessions Tab Content -->
                    <div x-show="activeTab === 'sessions'" x-transition>
                        @livewire('profile.logout-other-browser-sessions-form')
                    </div>

                    <!-- Danger Zone Tab Content -->
                    <div x-show="activeTab === 'danger'" x-transition>
                        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                            @livewire('profile.delete-user-form')
                        @endif
                    </div>
                    
                </div>
            </div>

        </div>
    </div>

    <!-- Scoped Clean stylesheet to format Jetstream forms to align perfectly -->
    <style>
        /* Sidebar Navigation Menu */
        .account-sidebar-menu {
            background: white !important;
            border-radius: 24px !important;
            padding: 15px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.01) !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 6px !important;
            box-sizing: border-box !important;
        }

        .account-menu-item {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 14px 18px !important;
            border-radius: 12px !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            color: #64748b !important;
            background: transparent !important;
            border-left: 4px solid transparent !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            width: 100% !important;
            box-sizing: border-box !important;
            user-select: none !important;
            text-align: left !important;
        }

        .account-menu-item:hover {
            color: #4f46e5 !important;
            background: #f8fafc !important;
            transform: translateX(4px) !important;
        }

        .account-menu-item svg {
            transition: transform 0.2s ease !important;
        }

        .account-menu-item:hover svg {
            transform: scale(1.1) !important;
        }

        .account-menu-item.active {
            background: #e0e7ff !important;
            color: #4338ca !important;
            font-weight: 700 !important;
            border-left: 4px solid #4f46e5 !important;
        }

        .account-menu-item.danger-item {
            color: #64748b !important;
        }

        .account-menu-item.danger-item:hover {
            color: #e11d48 !important;
            background: #fff1f2 !important;
            transform: translateX(4px) !important;
        }

        .account-menu-item.danger-item.active {
            background: #ffe4e6 !important;
            color: #be123c !important;
            font-weight: 700 !important;
            border-left: 4px solid #e11d48 !important;
        }

        /* Form containers alignment and generous section spacing */
        .profile-forms-wrapper [class*="grid"] {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 16px !important;
            margin-bottom: 45px !important;
        }

        @media (min-width: 768px) {
            .profile-forms-wrapper [class*="grid"] {
                grid-template-columns: 280px 1fr !important;
                gap: 30px !important;
            }
        }

        /* Section Titles and Descriptions (Left Column) */
        .profile-forms-wrapper [class*="col-span-1"] {
            padding: 0 5px !important;
            margin-top: 5px !important;
        }

        .profile-forms-wrapper [class*="col-span-1"] h3 {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            margin: 0 0 8px 0 !important;
        }

        .profile-forms-wrapper [class*="col-span-1"] p {
            font-size: 0.85rem !important;
            color: #64748b !important;
            line-height: 1.5 !important;
            margin: 0 !important;
        }

        /* Inner Card Headings & Text (Right Column Card Body) */
        .profile-forms-wrapper [class*="col-span-2"] h3 {
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 0 10px 0 !important;
        }

        .profile-forms-wrapper [class*="col-span-2"] p {
            font-size: 0.85rem !important;
            color: #475569 !important;
            line-height: 1.5 !important;
            margin: 0 !important;
        }

        /* Premium Card Containers (Form and Action Section Cards) */
        .profile-forms-wrapper form,
        .profile-forms-wrapper [class*="col-span-2"] > div {
            display: flex !important;
            flex-direction: column !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015) !important;
            background: white !important;
            overflow: hidden !important;
            transition: all 0.3s ease !important;
            box-sizing: border-box !important;
            width: 100% !important;
        }

        .profile-forms-wrapper form:hover,
        .profile-forms-wrapper [class*="col-span-2"] > div:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03) !important;
            border-color: #cbd5e1 !important;
        }

        /* Strip double borders, shadows, and radii inside the main card containers */
        .profile-forms-wrapper form > div {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            background: transparent !important;
        }

        /* Form Card Content Padding */
        .profile-forms-wrapper form > div:first-child,
        .profile-forms-wrapper [class*="col-span-2"] > div {
            padding: 28px !important;
        }

        /* Form Action Footer styling (Merged cleanly into card) */
        .profile-forms-wrapper form > div:nth-child(2) {
            background-color: #f8fafc !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 20px 28px !important;
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 12px !important;
            margin-top: 0 !important;
        }

        /* Force standard inputs stacking inside card to prevent grids overlapping */
        .profile-forms-wrapper [class*="grid-cols-6"] {
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
        }

        /* Clean inputs design */
        .profile-forms-wrapper input[type="text"],
        .profile-forms-wrapper input[type="email"],
        .profile-forms-wrapper input[type="password"] {
            width: 100% !important;
            height: 44px !important;
            padding: 0 14px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            font-size: 0.85rem !important;
            color: #0f172a !important;
            transition: all 0.2s ease !important;
            box-sizing: border-box !important;
            background: #fff !important;
        }

        .profile-forms-wrapper input[type="text"]:focus,
        .profile-forms-wrapper input[type="email"]:focus,
        .profile-forms-wrapper input[type="password"]:focus {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
            outline: none !important;
        }

        /* Form labels styling */
        .profile-forms-wrapper label {
            display: block !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            margin-bottom: 6px !important;
        }

        /* Avatar styling inside profile info form */
        .profile-forms-wrapper [class*="size-20"] {
            width: 72px !important;
            height: 72px !important;
            border-radius: 50% !important;
            display: block !important;
            object-fit: cover !important;
            border: 2px solid #e2e8f0 !important;
            margin-bottom: 12px !important;
        }

        /* Premium Primary & Action Buttons */
        .profile-forms-wrapper button:not([class*="secondary"]):not([class*="danger"]):not(.underline) {
            background: linear-gradient(to right, #4f46e5, #6366f1) !important;
            color: white !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            padding: 10px 20px !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .profile-forms-wrapper button:not([class*="secondary"]):not([class*="danger"]):not(.underline):hover {
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25) !important;
            transform: translateY(-1px) !important;
            color: white !important;
        }

        /* Premium Secondary Buttons */
        .profile-forms-wrapper button[class*="secondary"] {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            padding: 10px 18px !important;
            border: 1px solid #e2e8f0 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            text-transform: uppercase !important;
            letter-spacing: 0.03em !important;
            box-shadow: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .profile-forms-wrapper button[class*="secondary"]:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        /* Premium Danger Buttons */
        .profile-forms-wrapper button[class*="danger"] {
            background: linear-gradient(to right, #ef4444, #dc2626) !important;
            color: white !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            padding: 10px 20px !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .profile-forms-wrapper button[class*="danger"]:hover {
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25) !important;
            transform: translateY(-1px) !important;
            color: white !important;
        }

        /* Status update text */
        .profile-forms-wrapper form .text-sm.text-gray-600 {
            font-size: 0.8rem !important;
            color: #10b981 !important;
            font-weight: 600 !important;
        }

        /* --- Dark Mode Styles Integration --- */
        .profile-header-panel {
            background: white !important;
            border-radius: 20px !important;
            padding: 25px !important;
            margin-bottom: 30px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.01) !important;
        }
        .profile-header-title {
            font-size: 1.8rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            margin: 0 !important;
        }
        .profile-header-subtitle {
            font-size: 0.85rem !important;
            color: #64748b !important;
            margin: 5px 0 0 0 !important;
            font-weight: 500 !important;
        }

        /* Dark Mode overrides */
        .dark .profile-header-panel {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: rgba(51, 65, 85, 0.5) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }
        .dark .profile-header-title {
            color: #f1f5f9 !important;
        }
        .dark .profile-header-subtitle {
            color: #94a3b8 !important;
        }

        .dark .account-sidebar-menu {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: rgba(51, 65, 85, 0.5) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        }

        .dark .account-menu-item {
            color: #94a3b8 !important;
        }
        .dark .account-menu-item:hover {
            color: #818cf8 !important;
            background: rgba(30, 41, 59, 0.5) !important;
        }
        .dark .account-menu-item.active {
            background: rgba(79, 70, 229, 0.2) !important;
            color: #c7d2fe !important;
            border-left-color: #818cf8 !important;
        }
        .dark .account-menu-item.danger-item {
            color: #94a3b8 !important;
        }
        .dark .account-menu-item.danger-item:hover {
            color: #f43f5e !important;
            background: rgba(225, 29, 72, 0.15) !important;
        }
        .dark .account-menu-item.danger-item.active {
            background: rgba(225, 29, 72, 0.25) !important;
            color: #fda4af !important;
            border-left-color: #f43f5e !important;
        }

        .dark .profile-forms-wrapper form,
        .dark .profile-forms-wrapper [class*="col-span-2"] > div {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: rgba(51, 65, 85, 0.5) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }
        .dark .profile-forms-wrapper form:hover,
        .dark .profile-forms-wrapper [class*="col-span-2"] > div:hover {
            border-color: rgba(99, 102, 241, 0.4) !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
        }

        .dark .profile-forms-wrapper form > div:nth-child(2) {
            background-color: rgba(30, 41, 59, 0.4) !important;
            border-top: 1px solid rgba(51, 65, 85, 0.5) !important;
        }

        .dark .profile-forms-wrapper input[type="text"],
        .dark .profile-forms-wrapper input[type="email"],
        .dark .profile-forms-wrapper input[type="password"] {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .dark .profile-forms-wrapper input[type="text"]:focus,
        .dark .profile-forms-wrapper input[type="email"]:focus,
        .dark .profile-forms-wrapper input[type="password"]:focus {
            border-color: #818cf8 !important;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2) !important;
        }

        .dark .profile-forms-wrapper [class*="col-span-1"] h3 {
            color: #f1f5f9 !important;
        }
        .dark .profile-forms-wrapper [class*="col-span-1"] p {
            color: #94a3b8 !important;
        }
        .dark .profile-forms-wrapper [class*="col-span-2"] h3 {
            color: #f1f5f9 !important;
        }
        .dark .profile-forms-wrapper [class*="col-span-2"] p {
            color: #cbd5e1 !important;
        }
        .dark .profile-forms-wrapper label {
            color: #94a3b8 !important;
        }

        /* Additional Jetstream inner component styling in dark mode */
        .dark .profile-forms-wrapper [class*="col-span-2"] [class*="text-gray-"],
        .dark .profile-forms-wrapper [class*="col-span-2"] [class*="text-slate-"] {
            color: #cbd5e1 !important;
        }
        .dark .profile-forms-wrapper [class*="col-span-2"] [class*="bg-gray-"],
        .dark .profile-forms-wrapper [class*="col-span-2"] [class*="bg-slate-"] {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
    </style>
</x-app-layout>
