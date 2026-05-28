<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    // @use HasFactory<UserFactory>
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    // The attributes that are mass assignable.
    use \App\Traits\BelongsToTenant;

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'tenant_id',
        'google_id',
        'profile_photo_path',
    ];

    protected static function booted()
    {
        // For self-registered Admins, set their own ID as the tenant_id
        static::created(function ($user) {
            if (empty($user->tenant_id) && $user->role === 'Admin') {
                $user->tenant_id = $user->id;
                $user->saveQuietly();
            }
        });
    }

    // ── Role helpers ──────────────────────────────────────

    public function isAdmin()
    {
        return $this->role === 'Admin';
    }

    public function canEdit()
    {
        return $this->isAdmin() || $this->role === 'Staff' || $this->role === 'User';
    }

    public function canDelete()
    {
        return $this->isAdmin() || $this->role === 'Staff' || $this->role === 'User';
    }

    // ── Scopes ────────────────────────────────────────────

    // Scope: only active users.
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: only admin users.
    public function scopeAdmins($query)
    {
        return $query->where('role', 'Admin');
    }

    // Override default profile photo URL to use username when name is empty.
    // Generates initials-based avatar via ui-avatars.com as fallback.
    protected function defaultProfilePhotoUrl()
    {
        $displayName = $this->name ?: $this->username ?: 'U';
        $words = preg_split('/[\s\-_]+/', $displayName);
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1));
            }
        }
        $initials = mb_substr($initials, 0, 2);
        if (empty($initials)) {
            $initials = 'U';
        }

        // Generate a deterministic gradient color scheme based on user name/username
        $hash = md5($displayName);
        $hue1 = hexdec(substr($hash, 0, 2)) % 360;
        $hue2 = ($hue1 + 45) % 360;
        
        $color1 = "hsl({$hue1}, 80%, 65%)";
        $color2 = "hsl({$hue2}, 85%, 50%)";

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
            . '<defs>'
            . '<linearGradient id="grad-' . $this->id . '" x1="0%" y1="0%" x2="100%" y2="100%">'
            . '<stop offset="0%" stop-color="' . $color1 . '" />'
            . '<stop offset="100%" stop-color="' . $color2 . '" />'
            . '</linearGradient>'
            . '</defs>'
            . '<rect width="100" height="100" rx="30" fill="url(#grad-' . $this->id . ')" />'
            . '<text x="50" y="55" font-family="\'Plus Jakarta Sans\', \'Inter\', sans-serif" font-weight="700" font-size="38" fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle">'
            . $initials
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    // Get the user's name, fallback to username or default if empty.
    protected function name(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ?: ($this->username ? ucfirst($this->username) : 'User'),
        );
    }

    // Get the user's email, fallback to a local domain representation if empty.
    protected function email(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ?: ($this->username ? $this->username . '@example.com' : 'user@example.com'),
        );
    }

    // Get the URL to the user's profile photo.
    protected function profilePhotoUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(function (): string {
            if ($this->profile_photo_path) {
                if (str_starts_with($this->profile_photo_path, 'http://') || str_starts_with($this->profile_photo_path, 'https://')) {
                    return $this->profile_photo_path;
                }
                return asset('storage/' . $this->profile_photo_path);
            }
            return $this->defaultProfilePhotoUrl();
        });
    }

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    // The accessors to append to the model's array form.
    protected $appends = [
        'profile_photo_url',
    ];

    // Get the attributes that should be cast.
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Send the password reset notification.
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    // Get the orders associated with this user.
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
