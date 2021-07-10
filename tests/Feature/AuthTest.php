<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        User::create([
            'name'=> 'Valid User',
            'email'=> 'validuser@email.com',
            'password'=> bcrypt('validpassword'),
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> true,
        ]);

        $this->validCredential = [
            'email'=> 'validuser@email.com',
            'password'=> 'validpassword',
        ];

        $this->invalidPassword = [
            'email'=> 'validuser@email.com',
            'password'=> 'invalidpassword',
        ];

        $this->invalidCredential = [
            'email'=> 'invaliduser@email.com',
            'password'=> 'invalidpassword',
        ];
    }

    /** 
     * User's authentication test.
     * Check if token is acquired correctly if credential is correct
    */
    public function test_login_with_valid_credential(): void
    {
        $response = $this->postJson('/api/login/', $this->validCredential);
        $response->assertOk()
                 ->assertJson(['token'=> $response['token']]);
    }
    
    /** 
     * Try to log in with a registered email, but a wrong password
    */
    public function test_login_with_valid_email_but_invalid_password(): void
    {
        $response = $this->postJson('/api/login/', $this->invalidPassword);
        $response->assertStatus(401)
                 ->assertJson(['message'=>'Incorrect password']);
    }

    /**
     * Try to log in with a non-registered email
    */
    public function test_login_with_invalid_credential(): void
    {
        $response = $this->postJson('/api/login/', $this->invalidCredential);
        $response->assertStatus(401)
                 ->assertJson(['message'=>'No account is registered using invaliduser@email.com']);
    }

    public function test_logout_while_logged_in(): void
    {
        $login = $this->postJson('/api/login/', $this->validCredential);

        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$login['token']
        ])->postJson('/api/logout/');
        $response->assertOk();
    }

    public function test_logout_while_not_logged_in(): void
    {
        $response = $this->postJson('/api/logout/');
        $response->assertStatus(401);
    }
}
