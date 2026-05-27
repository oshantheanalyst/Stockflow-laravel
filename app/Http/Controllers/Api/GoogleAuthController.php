<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    // Redirect the user to the Google OAuth page.
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->stateless()->redirect();
        } catch (\Exception $e) {
            Log::error('Google redirect failed: ' . $e->getMessage());
            return redirect()->to(url('/login'))->withErrors(['error' => 'Unable to connect to Google: ' . $e->getMessage()]);
        }
    }

    // Handle the callback from Google OAuth.
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            if (!isset($googleUser->user['email_verified']) || $googleUser->user['email_verified'] !== true) {
                return redirect()->to(url('/login'))->withErrors(['error' => 'Your Google email is not verified. Please verify it first.']);
            }

            $email = $googleUser->getEmail();
            if (empty($email)) {
                return redirect()->to(url('/login'))->withErrors(['error' => 'Unable to retrieve your email address from Google.']);
            }

            $user = User::withoutGlobalScopes()->where('google_id', $googleUser->getId())->first();

            if (!$user) {
                $user = User::withoutGlobalScopes()->where('email', $email)->first();
                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'profile_photo_path' => $user->profile_photo_path ?: $googleUser->getAvatar(),
                    ]);
                } else {
                    $googleName = $googleUser->getName() ?: ($googleUser->name ?? 'user');
                    $baseUsername = Str::slug($googleName);
                    if (empty($baseUsername)) {
                        $baseUsername = 'user';
                    }
                    
                    $username = $baseUsername . rand(1000, 9999);
                    while (User::withoutGlobalScopes()->where('username', $username)->exists()) {
                        $username = $baseUsername . rand(1000, 9999);
                    }

                    $user = User::create([
                        'name' => $googleUser->getName() ?: $username,
                        'username' => $username,
                        'email' => $email,
                        'google_id' => $googleUser->getId(),
                        'password' => Hash::make(Str::random(32)),
                        'role' => 'Admin', // All self-registered users are Admins
                        'profile_photo_path' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                        'is_active' => true,
                    ]);
                }
            }

            if (!$user->is_active) {
                return redirect()->to(url('/login'))->withErrors(['error' => 'Your account has been deactivated. Please contact support.']);
            }

            $token = $user->createToken('google-token')->plainTextToken;

            $userPayload = [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'profile_photo_url' => $user->profile_photo_url,
            ];

            $cookie = cookie('api_token', $token, 1440, '/', null, false, false, false, 'Lax');

            return redirect()->to(
                url('/login') . 
                '?google_token=' . urlencode($token) . 
                '&google_user=' . urlencode(json_encode($userPayload))
            )->withCookie($cookie);

        } catch (\Exception $e) {
            Log::error('Google callback failed: ' . $e->getMessage());
            return redirect()->to(url('/login'))->withErrors(['error' => 'Authentication failed: ' . $e->getMessage()]);
        }
    }
}
