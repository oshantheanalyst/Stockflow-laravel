<x-action-section>
    <x-slot name="title">
        {{ __('Browser Sessions') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Manage and log out your active sessions on other browsers and devices.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Your complete session history is shown below, including past sessions.') }}
        </div>

        {{-- ── Active Sessions ──────────────────────────────────────────────── --}}
        @if (count($this->sessions) > 0)
            <h4 class="mt-6 mb-3 text-sm font-semibold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v2m0 18v2m8.66-15.66l-1.41 1.41M4.75 19.25l-1.41 1.41M23 12h-2M3 12H1m18.66 7.66l-1.41-1.41M4.75 4.75L3.34 3.34"/></svg>
                {{ __('Active Sessions') }}
            </h4>
            <div class="space-y-4">
                @foreach ($this->sessions as $session)
                    <div class="flex items-center justify-between p-3 rounded-lg border border-green-100 bg-green-50/50">
                        <div class="flex items-center">
                            <div>
                                @if ($session->agent->isDesktop())
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                    </svg>
                                @endif
                            </div>

                            <div class="ms-3">
                                <div class="text-sm text-gray-600 font-medium">
                                    {{ $session->agent->platform() ? $session->agent->platform() : __('Unknown') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('Unknown') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $session->ip_address }},
                                    @if ($session->is_current_device)
                                        <span class="text-green-500 font-semibold">{{ __('This device') }}</span>
                                    @else
                                        {{ __('Last active') }} {{ $session->last_active }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Individual terminate button (not for current device) --}}
                        @if (! $session->is_current_device)
                            <button wire:click="terminateToken({{ $session->token_id }})"
                                    wire:loading.attr="disabled"
                                    wire:confirm="Are you sure you want to terminate this session?"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-md hover:bg-red-100 transition-colors border border-red-200"
                                    title="Terminate this session">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                {{ __('Terminate') }}
                            </button>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-600 text-xs font-semibold rounded-md border border-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('Current') }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── Session History (Active + Past) ──────────────────────────────── --}}
        @if (count($this->sessionHistory) > 0)
            <h4 class="mt-8 mb-3 text-sm font-semibold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('Session History') }}
            </h4>
            <div class="space-y-3">
                @foreach ($this->sessionHistory as $history)
                    <div class="flex items-center justify-between p-3 rounded-lg border
                        {{ $history->status === 'active' ? 'border-green-100 bg-green-50/30' : 'border-gray-100 bg-gray-50/50' }}">
                        <div class="flex items-center">
                            <div>
                                @if ($history->agent->isDesktop())
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="size-7 {{ $history->status === 'active' ? 'text-green-500' : 'text-gray-400' }}">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="size-7 {{ $history->status === 'active' ? 'text-green-500' : 'text-gray-400' }}">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                    </svg>
                                @endif
                            </div>

                            <div class="ms-3">
                                <div class="text-sm font-medium {{ $history->status === 'active' ? 'text-gray-700' : 'text-gray-400' }}">
                                    {{ $history->device_name }}
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-2 flex-wrap">
                                    <span>{{ $history->ip_address }}</span>
                                    <span class="text-gray-300">•</span>
                                    <span>{{ __('Signed in') }} {{ $history->logged_in_at }}</span>
                                    @if ($history->logged_out_at)
                                        <span class="text-gray-300">•</span>
                                        <span>{{ __('Ended') }} {{ $history->logged_out_at }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($history->is_current_device)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <span class="size-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                    {{ __('Current') }}
                                </span>
                            @elseif ($history->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full">
                                    <span class="size-1.5 bg-blue-500 rounded-full"></span>
                                    {{ __('Active') }}
                                </span>
                                <button wire:click="terminateHistory({{ $history->id }})"
                                        wire:loading.attr="disabled"
                                        wire:confirm="Are you sure you want to terminate this session?"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-md hover:bg-red-100 transition-colors border border-red-200"
                                        title="Terminate this session">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    {{ __('End') }}
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">
                                    <span class="size-1.5 bg-gray-400 rounded-full"></span>
                                    {{ __('Ended') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-6 p-4 rounded-lg border border-gray-100 bg-gray-50/50 text-center">
                <p class="text-sm text-gray-400">{{ __('No session history available yet. Session history will be recorded from your next login.') }}</p>
            </div>
        @endif

        <div class="flex items-center mt-5">
            <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('Log Out Other Browser Sessions') }}
            </x-button>

            <x-action-message class="ms-3" on="loggedOut">
                {{ __('Done.') }}
            </x-action-message>

            <x-action-message class="ms-3" on="sessionTerminated">
                {{ __('Session terminated.') }}
            </x-action-message>
        </div>

        <!-- Log Out Other Devices Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">
                {{ __('Log Out Other Browser Sessions') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}

                <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                                autocomplete="current-password"
                                placeholder="{{ __('Password') }}"
                                x-ref="password"
                                wire:model="password"
                                wire:keydown.enter="logoutOtherBrowserSessions" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-button class="ms-3"
                            wire:click="logoutOtherBrowserSessions"
                            wire:loading.attr="disabled">
                    {{ __('Log Out Other Browser Sessions') }}
                </x-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
