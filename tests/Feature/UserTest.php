<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{   
    public function setUp(): void
    {
        parent::setUp();

        User::create([
            'name'=> 'Super Admin',
            'email'=> 'validsuperadmin@email.com',
            'password'=> bcrypt('validpassword'),
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> true,
        ]);

        User::create([
            'name'=> 'Admin',
            'email'=> 'validadmin@email.com',
            'password'=> bcrypt('validpassword'),
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> false,
        ]);

        $this->superadmin_token = $this->postJson('/api/login/', [
            'email'=> 'validsuperadmin@email.com',
            'password'=> 'validpassword',
        ])['token'];

        $this->admin_token = $this->postJson('/api/login/', [
            'email'=> 'validadmin@email.com',
            'password'=> 'validpassword',
        ])['token'];

        $this->valid_create_user_payload = [
            'name'=> 'New Admin',
            'email'=> 'newadmin@email.com',
            'password'=> 'validpassword',
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> false,
        ];
    }

    public function test_get_all_admin_and_superadmin(): void
    {
        User::factory()->count(10)->create();
        $users = User::all();

        $response = $this->getJson('/api/users/');
        $response->assertOk()
                 ->assertJson($users);
    }

    public function test_get_admin_or_superadmin_detail(): void
    {
        User::factory()->count(1)->create();
        $user = User::where('id', 1);

        $response = $this->getJson('/api/users/1/');
        $response->assertOk()
                 ->assertJson($user);
    }

    public function test_get_admin_or_superadmin_detail_with_nonexisting_id(): void
    {
        $response = $this->getJson('/api/users/1/');
        $response->assertNotFound();
    }

    public function test_create_an_admin_with_superadmin_permission(): void
    {
        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->superadmin_token
        ])->postJson('/api/users/', $this->valid_create_user_payload);
        $response->assertCreated()
                 ->assertJson(User::where('id', $response['id']));
    }

    public function test_create_an_admin_with_admin_permission(): void
    {
        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->admin_token
        ])->postJson('/api/users/', $this->valid_create_user_payload);
        $response->assertForbidden();
    }

    public function test_create_an_admin_with_unauthorized_user(): void
    {
        $response = $this->postJson('/api/users/', $this->valid_create_user_payload);
        $response->assertUnauthorized();
    }
}
