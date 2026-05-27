<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Laravel\Socialite\Two\GoogleProvider;
use Tests\TestCase;
use Mockery;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // Test redirectToGoogle redirects to Google OAuth page.
    public function test_redirect_to_google_redirects_correctly(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('redirect')->andReturn(redirect()->to('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/api/auth/google/redirect');

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    // Test handleGoogleCallback rejects unverified Google email.
    public function test_callback_rejects_unverified_email(): void
    {
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->user = ['email_verified' => false]; // Unverified email

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect(url('/login'));
        $response->assertSessionHasErrors(['error']);
    }

    // Test handleGoogleCallback matches an existing user by google_id.
    public function test_callback_matches_existing_user_by_google_id(): void
    {
        $user = User::factory()->create([
            'google_id' => '1234567890',
            'email' => 'existing@example.com',
            'is_active' => true,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('1234567890');
        $googleUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $googleUser->user = ['email_verified' => true];

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect();
        $this->assertStringContainsString('google_token=', $response->headers->get('Location'));
        
        // Assert no duplicate user was created
        $this->assertEquals(1, User::withoutGlobalScopes()->where('email', 'existing@example.com')->count());
    }

    // Test handleGoogleCallback matches an existing user by email and links Google ID.
    public function test_callback_matches_existing_user_by_email_and_links_google_id(): void
    {
        $user = User::factory()->create([
            'google_id' => null,
            'email' => 'existing@example.com',
            'is_active' => true,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('987654321');
        $googleUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');
        $googleUser->user = ['email_verified' => true];

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect();

        // Verify the existing user is updated with the Google ID
        $user->refresh();
        $this->assertEquals('987654321', $user->google_id);
        $this->assertEquals(1, User::withoutGlobalScopes()->where('email', 'existing@example.com')->count());
    }

    // Test handleGoogleCallback creates new user with sanitized username if not exists.
    public function test_callback_creates_new_user_with_sanitized_username(): void
    {
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('999999999');
        $googleUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $googleUser->shouldReceive('getName')->andReturn('John Doe!! @@');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');
        $googleUser->user = ['email_verified' => true];

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect();

        // Check if user is created in database
        $user = User::withoutGlobalScopes()->where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('999999999', $user->google_id);
        
        // Assert username is sanitized (no special characters or symbols)
        $this->assertMatchesRegularExpression('/^[a-z0-9\-]+\d{4}$/', $user->username);
        $this->assertStringContainsString('john-doe', $user->username);
    }

    // Test handleGoogleCallback rejects deactivated user.
    public function test_callback_rejects_deactivated_user(): void
    {
        $user = User::factory()->create([
            'google_id' => 'deactivated-google-id',
            'email' => 'deactivated@example.com',
            'is_active' => false, // Deactivated account
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('deactivated-google-id');
        $googleUser->shouldReceive('getEmail')->andReturn('deactivated@example.com');
        $googleUser->user = ['email_verified' => true];

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect(url('/login'));
        $response->assertSessionHasErrors(['error']);
    }

    // Test handleGoogleCallback does NOT create a web session.
    public function test_callback_does_not_create_web_session(): void
    {
        $user = User::factory()->create([
            'google_id' => 'web-session-test-id',
            'email' => 'websession@example.com',
            'is_active' => true,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('web-session-test-id');
        $googleUser->shouldReceive('getEmail')->andReturn('websession@example.com');
        $googleUser->user = ['email_verified' => true];

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect();
        
        // Assert web guard remains unauthenticated
        $this->assertFalse(\Illuminate\Support\Facades\Auth::guard('web')->check());
    }
}
