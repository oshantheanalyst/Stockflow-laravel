<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    // Validate and create a newly registered user. Every self-registered user automatically becomes an Admin.
    public function create(array $input): User
    {
        Validator::make($input, [
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => $this->passwordRules(),
            'terms'    => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'username.unique'     => 'That username is already taken. Please choose a different one.',
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
            'email.unique'        => 'An account with that email already exists.',
        ])->validate();

        return User::create([
            'name'                => $input['name'],
            'username'            => $input['username'],
            'email'               => $input['email'],
            'email_verified_at'   => now(),
            'password'            => Hash::make($input['password']),
            'role'                => 'Admin',   // All self-registered users are Admins (business owners)
            'is_active'           => true,
        ]);
    }
}
