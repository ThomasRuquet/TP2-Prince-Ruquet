<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user()
    {
        $adminRole = Role::create(['name' => 'admin']);

        $data = [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'login' => 'test',
            'role_id' => $adminRole->id,
        ];

        $response = $this->postJson('/api/signup', $data);

        $response->assertJson([
            'message' => 'User registered successfully',
        ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@example.com',
            'login' => 'test'
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        //https://stackoverflow.com/questions/35715755/how-to-compare-two-encryptedbcrypt-password-in-laravel
        $this->assertTrue(Hash::check('Password123', $user->password));
        $response->assertStatus(CREATED);
    }

    public function test_register_fails_with_invalid_first_name()
    {
        $adminRole = Role::create(['name' => 'admin']);

        $data = [
            'first_name' => '',
            'last_name' => 'test',
            'email' => 'test@example.com',
            'password' => 'testtest123',
            'login' => '124',
            'role_id' => $adminRole->id,
        ];

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(INVALID_DATA);
        $response->assertJsonValidationErrors(['first_name']);
    }

    public function test_register_fails_with_invalid_email()
    {
        $adminRole = Role::create(['name' => 'admin']);

        $data = [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'testInvalid',
            'password' => 'testtest123',
            'login' => '124',
            'role_id' => $adminRole->id,
        ];

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(INVALID_DATA);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_invalid_password()
    {
        $adminRole = Role::create(['name' => 'admin']);

        $data = [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@example.com',
            'password' => 'test',
            'login' => '124',
            'role_id' => $adminRole->id,
        ];

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(INVALID_DATA);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_register_fails_with_invalid_last_name()
    {
        $adminRole = Role::create(['name' => 'admin']);

        $data = [
            'first_name' => 'test',
            'last_name' => '',
            'email' => 'test@example.com',
            'password' => 'testtest123',
            'login' => '124',
            'role_id' => $adminRole->id,
        ];

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(INVALID_DATA);
        $response->assertJsonValidationErrors(['last_name']);
    }

    public function test_register_fails_with_a_duplicate_login()
    {
        $adminRole = Role::create(['name' => 'admin']);

        $existingUser = User::factory()->create([
            'login' => 'test'
        ]);

        $data = [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@example.com',
            'password' => 'testtest123',
            'login' => 'test',
            'role_id' => $adminRole->id,
        ];

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(INVALID_DATA);
        $response->assertJsonValidationErrors(['login']);
    }

    public function test_login_returns_token()
    {
        Role::create(['name' => 'admin']);

        $user = User::factory()->create([
            'password' => 'Password123'
        ]);

        $data = [
            'login' => $user->login,
            'password' => 'Password123'
        ];

        $response = $this->postJson('/api/signin', $data);

        $response->assertStatus(OK);
        $response->assertJsonStructure([
            'access_token',
        ]);
    }
    public function test_login_fails_with_invalid_password()
    {
        Role::create(['name' => 'admin']);

        $user = User::factory()->create([
            'password' => 'Password123',
            'login' => 'test'
        ]);

        $data = [
            'login' => 'test',
            'password' => 'DifferentPassword'
        ];

        $response = $this->postJson('/api/signin', $data);

        $response->assertStatus(UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }
    public function test_login_fails_with_invalid_login()
    {
        Role::create(['name' => 'admin']);

        $user = User::factory()->create([
            'password' => 'Password123',
            'login' => 'test'
        ]);

        $data = [
            'login' => 'differentLogin',
            'password' => 'Password123'
        ];

        $response = $this->postJson('/api/signin', $data);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_logout_revokes_token()
    {
        Role::create(['name' => 'admin']);

        $user = User::factory()->create([
            'password' => 'Password123',
            'login' => 'test'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        ##https://laravel.com/docs/13.x/http-tests
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/signout');

        $response->assertStatus(OK);
        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token',
        ]);
    }
    public function test_logout_fails_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalidtoken',
        ])->postJson('/api/signout');

        $response->assertStatus(UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);

    }
}
