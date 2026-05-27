<?php

namespace App\Livewire;

use App\Models\SessionHistory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Agent;
use Livewire\Component;

class LogoutOtherBrowserSessionsForm extends Component
{
    // Indicates if logout is being confirmed
    public $confirmingLogout = false;

    // The user's current password
    public $password = '';

    // Confirm logout from other browser sessions
    public function confirmLogout()
    {
        $this->password = '';

        $this->dispatch('confirming-logout-other-browser-sessions');

        $this->confirmingLogout = true;
    }

    // Log out from other browser sessions
    public function logoutOtherBrowserSessions(StatefulGuard $guard)
    {
        $this->resetErrorBag();

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $this->deleteOtherSessionRecords();

        $this->confirmingLogout = false;

        $this->dispatch('loggedOut');
    }

    // Terminate an active session by its Sanctum token ID
    public function terminateToken($tokenId)
    {
        // Delete token
        Auth::user()->tokens()->where('id', $tokenId)->delete();

        // Mark history as revoked
        SessionHistory::where('user_id', Auth::id())
            ->where('token_id', $tokenId)
            ->where('status', 'active')
            ->update([
                'status' => 'revoked',
                'logged_out_at' => now(),
            ]);

        $this->dispatch('sessionTerminated');
    }

    // Terminate a session by its history ID
    public function terminateHistory($sessionHistoryId)
    {
        $session = SessionHistory::where('id', $sessionHistoryId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $session) {
            return;
        }

        // If the session has an active token, delete it
        if ($session->token_id && $session->status === 'active') {
            Auth::user()->tokens()->where('id', $session->token_id)->delete();
        }

        // Mark as revoked
        $session->update([
            'status' => 'revoked',
            'logged_out_at' => now(),
        ]);

        $this->dispatch('sessionTerminated');
    }

    // Delete the other browser session records from storage
    protected function deleteOtherSessionRecords()
    {
        $currentTokenId = optional(Auth::user()->currentAccessToken())->id;

        // Mark other sessions as revoked in history
        SessionHistory::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('token_id', '!=', $currentTokenId)
            ->update(['status' => 'revoked', 'logged_out_at' => now()]);

        // Delete the actual Sanctum tokens
        Auth::user()->tokens()->where('id', '!=', $currentTokenId)->delete();
    }

    // Get current active sessions (API tokens)
    public function getSessionsProperty()
    {
        return Auth::user()->tokens->sortByDesc('last_used_at')->map(function ($token) {
            $userAgent = str_starts_with($token->name, 'stockflow-api-token') ? 'Unknown Agent' : $token->name;

            return (object) [
                'agent' => $this->createAgent((object) ['user_agent' => $userAgent]),
                'ip_address' => 'Token ID: ' . $token->id,
                'is_current_device' => $token->id === optional(Auth::user()->currentAccessToken())->id,
                'last_active' => $token->last_used_at ? Carbon::parse($token->last_used_at)->diffForHumans() : 'Never',
                'token_id' => $token->id,
            ];
        })->values();
    }

    // Get full session history
    public function getSessionHistoryProperty()
    {
        $currentTokenId = optional(Auth::user()->currentAccessToken())->id;

        return SessionHistory::where('user_id', Auth::id())
            ->orderByDesc('logged_in_at')
            ->limit(50)
            ->get()
            ->map(function ($session) use ($currentTokenId) {
                $agent = $this->createAgent((object) ['user_agent' => $session->user_agent ?? 'Unknown Agent']);

                return (object) [
                    'id' => $session->id,
                    'agent' => $agent,
                    'device_name' => $session->device_name,
                    'ip_address' => $session->ip_address ?? 'Unknown',
                    'status' => $session->status,
                    'is_current_device' => $session->token_id === $currentTokenId && $session->status === 'active',
                    'logged_in_at' => $session->logged_in_at ? $session->logged_in_at->diffForHumans() : 'Unknown',
                    'logged_out_at' => $session->logged_out_at ? $session->logged_out_at->diffForHumans() : null,
                    'last_active' => $session->logged_out_at
                        ? 'Ended ' . $session->logged_out_at->diffForHumans()
                        : ($session->logged_in_at ? $session->logged_in_at->diffForHumans() : 'Unknown'),
                ];
            });
    }

    // Create a new agent instance
    protected function createAgent($session)
    {
        return tap(new Agent(), fn ($agent) => $agent->setUserAgent($session->user_agent));
    }

    // Render component
    public function render()
    {
        return view('profile.logout-other-browser-sessions-form');
    }
}
