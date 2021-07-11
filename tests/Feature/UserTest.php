<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserTest extends TestCase
{   
    public function setUp(): void
    {
        parent::setUp();

        User::factory()->count(5)->create();

        $this->valid_create_user_payload = [
            'name'=> 'New Admin',
            'email'=> 'newadmin@email.com',
            'password'=> 'validpassword',
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> false,
        ];
    }

    /** 
     * User Read Test Cases
    */
    public function test_get_all_admin_and_superadmin(): void
    {
        $users_data = UserResource::collection(User::all());
        
        $response = $this->getJson('/api/users/');
        $response->assertOk()
                 ->assertJson($users_data->jsonSerialize());
    }

    public function test_get_admin_or_superadmin_detail(): void
    {
        $id = rand(1, 7);
        $user_data = new UserResource(User::find($id));

        $response = $this->getJson('/api/users/'.$id.'/');
        $response->assertOk()
                 ->assertJson($user_data->jsonSerialize());
    }

    public function test_get_admin_or_superadmin_detail_with_nonexisting_id(): void
    {
        $response = $this->getJson('/api/users/10/');
        $response->assertNotFound();
    }

    /** 
     * User Create Test Cases
    */
    public function test_create_an_admin_with_superadmin_permission(): void
    {
        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->superadmin_token
        ])->postJson('/api/users/', $this->valid_create_user_payload);
        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email'=> $this->valid_create_user_payload['email']
        ]);
    }

    public function test_create_an_admin_with_admin_permission(): void
    {
        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->admin_token
        ])->postJson('/api/users/', $this->valid_create_user_payload);
        $response->assertForbidden()
                 ->assertJson(['message'=> 'You do not have permission to do this action']);
    }

    public function test_create_an_admin_with_unauthorized_user(): void
    {
        $response = $this->postJson('/api/users/', $this->valid_create_user_payload);
        $response->assertUnauthorized();
    }

    /** 
     * User Update Test Cases
    */
    public function test_update_an_admin_with_superadmin_permission(): void
    {
        $id = rand(2, 7);

        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->superadmin_token
        ])->putJson('/api/users/'.$id.'/', $this->valid_create_user_payload);
        $user_data = new UserResource(User::find($id));
        $response->assertOk()
                 ->assertJson($user_data->jsonSerialize());
    }

    public function test_update_an_admin_with_admin_permission(): void
    {
        $id = rand(2, 7);

        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->admin_token
        ])->putJson('/api/users/'.$id.'/', $this->valid_create_user_payload);
        $response->assertForbidden()
                 ->assertJson(['message'=> 'You do not have permission to do this action']);
    }

    public function test_update_an_admin_with_unauthorized_user(): void
    {
        $id = rand(2, 7);

        $response = $this->putJson('/api/users/'.$id.'/', $this->valid_create_user_payload);
        $response->assertUnauthorized();
    }

    public function test_update_an_admin_nonexisiting_id(): void
    {
        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->superadmin_token
        ])->putJson('/api/users/10/', $this->valid_create_user_payload);
        $response->assertNotFound();
    }


    /** 
     * User Read Test Cases
    */
    public function test_delete_an_admin_with_superadmin_permission(): void
    {
        $id = rand(2, 7);
        $user = User::find($id);

        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->superadmin_token
        ])->deleteJson('/api/users/'.$id.'/');
        $response->assertNoContent();

        $this->assertDeleted($user);
    }

    public function test_delete_an_admin_with_admin_permission(): void
    {
        $id = rand(2, 7);

        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->admin_token
        ])->deleteJson('/api/users/5/');
        $response->assertForbidden()
                 ->assertJson(['message'=> 'You do not have permission to do this action']);
    }

    public function test_delete_an_admin_with_unauthorized_user(): void
    {
        $id = rand(2, 7);

        $response = $this->deleteJson('/api/users/5/');
        $response->assertUnauthorized();
    }

    public function test_delete_an_admin_with_nonexisting_id(): void
    {
        $response = $this->withHeaders([
            'Authorization'=> 'Bearer '.$this->superadmin_token
        ])->deleteJson('/api/users/10/');
        $response->assertNotFound();
    }
}
