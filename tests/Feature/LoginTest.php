<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    
    private $user = null;
    private $token = null;
    private $email = "joe@example.com";
    private $password = "strongpassword";

    protected function setUp(): void
    {
        parent::setUp();

        // Create a User with the following credentials
        $this->user = User::create([
            'name' => $this->email,
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ]);
    }

    private function logUserIn()
    {
        $this->token = $this->user->createToken('authToken')->plainTextToken;
    }

    /**
     * Unregistered users cannot log in
     */
    public function test_unregistered_users_cannot_log_in()
    {
        $payload = [
            'email' => "unregistered@example.com",
            'password' => $this->password
        ];

        $response = $this->postJson('/api/login', $payload)
            ->assertStatus(401);
    }

    /**
     * Registered users can log in
     */
    public function test_registered_users_can_log_in()
    {
        $payload = [
            'email' => $this->email,
            'password' => $this->password
        ];

        $response = $this->postJson('/api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure(
                ['token']
            );
    }

    /**
     * Unauthenticated users cannot see user information
     */
    public function test_unauthenticated_users_cannot_see_user_information()
    {
        $token = "invalidtoken123455";
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/user')
            ->assertStatus(401);
    }

    /**
     * Logged in users can see user information
     */
    public function test_logged_in_users_can_see_user_information()
    {
        $this->logUserIn();
        $token = $this->token;
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/user')
            ->assertStatus(200)
            ->assertJson([
                'email' => $this->email
            ]);
    }

    /**
     * Logged in users can log out
     */
    public function test_logged_in_users_can_log_out()
    {
        $this->logUserIn();
        $token = $this->token;
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout')
            ->assertStatus(204);
        
        $this->assertEquals(0, count($this->user->tokens));
    }

    /**
     * Unauthenticated users cannot log out
     */
    public function test_unauthenticated_users_cannot_log_out()
    {
        $token = "invalidtoken";
        
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout')
            ->assertStatus(401);
    }
}
