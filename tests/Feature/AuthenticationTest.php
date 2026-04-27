<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $this->switchTenant('clinic-a');

        $user = User::where('email', 'doctor@clinic-a.test')->first();

        Auth::login($user);

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $this->switchTenant('clinic-a');

        $credentials = [
            'email' => 'doctor@clinic-a.test',
            'password' => 'wrongpassword',
        ];

        $this->post('/login', $credentials);

        $this->assertGuest();
    }

    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $credentials = [
            'email' => 'nonexistent@test.com',
            'password' => 'password',
        ];

        $this->post('/login', $credentials);

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $this->assertAuthenticated();

        Auth::logout();

        $this->assertGuest();
    }

    public function test_password_is_hashed(): void
    {
        $this->switchTenant('clinic-a');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'testhashed@test.com',
            'password' => 'plaintext',
            'clinic_id' => 'clinic-a',
        ]);

        $this->assertTrue(Hash::check('plaintext', $user->password));
        $this->assertFalse(Hash::check('wrong', $user->password));
    }

    public function test_user_without_clinic_can_be_created(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $this->assertNotNull($superAdmin->id);
        $this->assertTrue($superAdmin->hasRole('super-admin'));
    }
}
