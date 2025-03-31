<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test successful user registration
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name']
        ]);
    }

    /**
     * Test registration validation for required fields
     */
    public function test_registration_requires_all_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
    }

    /**
     * Test registration with invalid email format
     */
    public function test_registration_requires_valid_email(): void
    {
        $userData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration with mismatched passwords
     */
    public function test_registration_requires_password_confirmation_match(): void
    {
        $userData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test successful login
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $password = 'Password123!';
        $user = User::factory()->create([
            'password' => bcrypt($password)
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                ],
                'access_token'
            ]);
    }

    /**
     * Test login with invalid credentials
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'WrongPassword123!'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Your provided credentials cannot be verified.'
            ]);
    }

    /**
     * Test protected route access without token
     */
    public function test_protected_route_cannot_be_accessed_without_token(): void
    {
        $response = $this->getJson('/api/v1/companies');

        $response->assertStatus(401);
    }

    /**
     * Test protected route access with valid token
     */
    public function test_protected_route_can_be_accessed_with_valid_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!')
        ]);
        $token = $user->createToken('access_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/companies');

        $response->assertStatus(200);
    }

    /**
     * Test user logout
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!')
        ]);
        $token = $user->createToken('access_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseMissing('personal_access_tokens', ['access_token' => $token]);
    }
}
