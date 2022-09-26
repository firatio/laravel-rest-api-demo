<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    
    private $user = null;
    private $email = "joe@example.com";
    private $password = "strongpassword";

    /**
     * Create a User to be used as a registered user in the tests
     */
    private function registerUser()
    {
        $this->user = User::create([
            'name' => $this->email,
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ]);
    }

    public function test_users_can_register_with_valid_information()
    {
        $payload = [
            'email' => "joe@example.com",
            'password' => "strongpassword"
        ];

        $response = $this->postJson('/api/register', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['id']);

        $responseContent = json_decode($response->getContent());
        $this->assertEquals($this->email, User::find($responseContent->id)->email);
    }

    public function test_users_cannot_register_with_invalid_email()
    {
        $payload = [
            'email' => "joe",
            'password' => "strongpassword"
        ];

        $response = $this->postJson('/api/register', $payload)
            ->assertStatus(400);
    }

    public function test_users_cannot_register_with_invalid_password()
    {
        $payload = [
            'email' => "joe@example.com",
            'password' => ""
        ];

        $response = $this->postJson('/api/register', $payload)
            ->assertStatus(400);
    }

    public function test_users_cannot_register_with_the_same_email_address_again()
    {
        $this->registerUser();

        // same email address with a different password
        $payload = [
            'email' => $this->email,
            'password' => "anotherstrongpassword"
        ];
        
        $response = $this->postJson('/api/register', $payload)
            ->assertStatus(400);
    }
}
