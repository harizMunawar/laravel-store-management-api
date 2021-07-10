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

        $this->invalidCredential = [
            'email'=> 'invaliduser@email.com',
            'password'=> 'invalidpassword',
        ];
    }

    /** 
     * User's authentication test.
     * Check if token is acquired correctly if credential is correct
    */
    public function test_login(): void
    {
        $response = $this->postJson('/api/login/', $this->invalidCredential);
        $response->assertStatus(401);

        $response = $this->postJson('/api/login/', $this->validCredential);
        $token = $response->dump();
        $response->assertOk()
                 ->assertTrue($token['token']);
    }

    public function test_logout():void
    {
        $login = $this->postJson('/api/login/', $this->validCredential)->dump();

        $response = $this->postJson('/api/logout/');
        $response->assertStatus(400);

        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '+$login['token']
        ])->postJson('/api/logout/');
        $response->assertOk();
    }
}
