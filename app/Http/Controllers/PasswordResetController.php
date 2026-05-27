<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    // Display the password reset request view.
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    // Handle a password reset link request.
    // Accepts either username or email address.
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $input = $request->validated();
        $emailOrUsername = $input['email'];

        // Find user by email or username
        $user = User::where('email', $emailOrUsername)
            ->orWhere('username', $emailOrUsername)
            ->first();

        if (!$user) {
            return back()->withInput($request->only('email'))->with('error', 'No account found with that username or email address.');
        }

        // Check if user has an email
        if (!$user->email) {
            return back()->withInput($request->only('email'))->with('error', 'This account does not have an email address associated with it.');
        }

        // Send the password reset link
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Password reset link sent! Check your email for instructions.');
        }

        return back()->withInput($request->only('email'))->with('error', 'Unable to send reset link. Please try again later.');
    }

    // Display the password reset form.
    public function showResetForm(Request $request, $token = null): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Handle the password reset.
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password reset successfully! Please log in with your new password.');
        }

        return back()->withInput($request->only('email'))->with('error', 'Invalid or expired reset token. Please try again.');
    }
}
