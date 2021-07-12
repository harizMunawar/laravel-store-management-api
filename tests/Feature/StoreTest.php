<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Store;
use App\Models\User;
use App\Http\Resources\StoreResource;

class StoreTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Store::factory()->count(5)->create();

        $this->valid_create_store_payload = [
            'name'=> 'New Store',
            'contact_number'=> '0912381323',
            'description'=> 'Hello we are a newly opened store'
        ];
    }

    public function test_get_all_stores_with_superadmin_permission(): void
    {
        $stores = StoreResource::collection(Store::all());

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->getJson('/api/stores/');
        $response->assertOk()
                 ->assertJson($stores->jsonSerialize());
    }

    /**
     * If /api/stores/ endpoint is requested by a store admin,
     * will return only store that the admin belong to.
     * Except if the requesting admin not owning any store,
     * return a 400 status.
    */
    public function test_get_all_stores_with_admin_permission(): void
    {
        // Testing for when the requesting admin is currently not owning any store
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->getJson('/api/stores/');
        $response->assertStatus(400)
                 ->assertJson(['message'=> 'validadmin@email.com currently not owning any store, please ask a superadmin to assign one to you.']);
    }

    public function test_get_all_stores_with_admin_permission2(): void
    {
        // Create a new user to be the owner of store 1
        User::factory()->count(1)->create(['id'=> 999]);
        $user = User::find(999);
        $user->store_id = 1;
        $user->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->getToken($user->id)])
                         ->getJson('/api/stores/');
        $response->assertOk();
    }

    public function test_get_all_stores_with_unauthorized_user(): void
    {
        $response = $this->getJson('/api/stores/');
        $response->assertUnauthorized();
    }

    public function test_get_a_store_with_superadmin_permission(): void
    {
        $store = new StoreResource(Store::find(1));

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->getJson('/api/stores/1/');
        $response->assertOk()
                 ->assertJson($store->jsonSerialize());
    }

    /** 
     * Again, if an admin requested this endpoint,
     * they are only allowed to access store that they own
    */
    public function test_get_a_store_with_admin_permission(): void
    {
        // Requesting the api using an admin that are not the owner of store 1
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->getJson('/api/stores/1/');
        $response->assertForbidden();
    }

    public function test_get_a_store_with_admin_permission2(): void
    {
        // Create a new user to be the owner of store 1
        User::factory()->count(1)->create();
        $user = User::find(3);
        $user->store_id = 1;
        $user->save();

        // Requesting the api using an admin that are the owner of store 1
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->getToken($user->id)])
                         ->getJson('/api/stores/1/');
        $response->assertOk();
    }

    public function test_get_a_store_with_unauthorized_store(): void
    {
        $response = $this->getJson('/api/stores/1/');
        $response->assertUnauthorized();
    }

    public function test_get_a_store_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->getJson('/api/stores/99/');
        $response->assertNotFound();
    }

    public function test_create_a_store_with_superadmin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/stores/', $this->valid_create_store_payload);
        $store = new StoreResource(Store::where('name', $response['name'])->first());
        $response->assertCreated()
                 ->assertJson($store->jsonSerialize());

        $this->assertDatabaseHas('stores', [
            'name'=> $response['name'],
            'description'=> $response['description']
        ]);
    }

    public function test_create_a_store_with_admin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->postJson('/api/stores/', $this->valid_create_store_payload);
        $response->assertForbidden();
    }

    public function test_create_a_store_with_unauthorized_user(): void
    {
        $response = $this->postJson('/api/stores/', $this->valid_create_store_payload);
        $response->assertUnauthorized();
    }

    public function test_update_a_store_with_superadmin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->putJson('/api/stores/1/', $this->valid_create_store_payload);
        $store = new StoreResource(Store::find(1));
        $response->assertOk()
                 ->assertJson($store->jsonSerialize());
        
        $this->assertDatabaseHas('stores', [
            'name'=> $this->valid_create_store_payload['name'],
            'contact_number'=> $this->valid_create_store_payload['contact_number'],
            'description'=> $this->valid_create_store_payload['description']
        ]);
    }

    /** 
     * Again again, only the store owner or superadmin can CRUD a store
    */ 
    public function test_update_a_store_with_admin_permission(): void
    {
        // Requesting the api using an admin that are not the owner of store 1
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->putJson('/api/stores/1/', $this->valid_create_store_payload);
        $response->assertForbidden();
    }

    public function test_update_a_store_with_admin_permission2(): void
    {
        // Create a new user to be the owner of store 1
        User::factory()->count(1)->create();
        $user = User::find(3);
        $user->store_id = 1;
        $user->save();

        // Requesting the api using an admin that are the owner of store 1
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->getToken($user->id)])
                         ->putJson('/api/stores/1/', $this->valid_create_store_payload);
        $response->assertOk();
        
        // Check if the record is updated on the database
        $this->assertDatabaseHas('stores', [
            'name'=> $this->valid_create_store_payload['name'],
            'contact_number'=> $this->valid_create_store_payload['contact_number'],
            'description'=> $this->valid_create_store_payload['description']
        ]);
    }

    public function test_update_a_store_with_unauthorized_user(): void
    {
        $response = $this->putJson('/api/stores/1/', $this->valid_create_store_payload);
        $response->assertUnauthorized();
    }

    public function test_update_a_store_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->putJson('/api/stores/99/', $this->valid_create_store_payload);
        $response->assertNotFound();
    }

    public function test_delete_a_store_with_superadmin_permission(): void
    {
        $store = Store::find(1);

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/stores/1/');
        $response->assertNoContent();

        // Check if the record is deleted from the database
        $this->assertDeleted($store);
    }

    /** 
     * Again again again, only the owner or superadmin can CRUD a store record
    */
    public function test_delete_a_store_with_admin_permission(): void
    {
        // Requesting the api using an admin that are not the owner of store 1
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->deleteJson('/api/stores/1/');
        $response->assertForbidden();
    }

    public function test_delete_a_store_with_admin_permission2(): void
    {
        // Create a new user to be the owner of store 1
        User::factory()->count(1)->create();
        $user = User::find(3);
        $user->store_id = 1;
        $user->save();
        $store_record = Store::find(1);
        $store = new StoreResource($store_record);

        // Requesting the api using an admin that are the owner of store 1
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->getToken($user->id)])
                         ->deleteJson('/api/stores/1/');
        $response->assertNoContent();
        
        // Check if the record is updated on the database
        $this->assertDeleted($store_record);
    }

    public function test_delete_a_store_with_unauthorized_use(): void
    {
        $response = $this->deleteJson('/api/stores/1/');
        $response->assertUnauthorized();
    }

    public function test_delete_a_store_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/stores/99/');
        $response->assertNotFound();
    }

    public function test_assigning_store_ownership_with_superadmin_permission(): void
    {
        // Creating two new users with null store id for testing purpose 
        User::factory()->count(1)->create();
        $new_user = User::find(3);

        // Requesting the api while the store have no owner
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/stores/1/owner/users/'.$new_user->id.'/');
        $response->assertOk();
        $user = User::find($new_user->id);
        $this->assertEquals($user->store_id, 1);
    }

    public function test_assigning_store_ownership_with_superadmin_permission2(): void
    {
        User::factory()->count(2)->create();
        $new_user_1 = User::find(3);
        $new_user_1->store_id = 1;
        $new_user_1->save();
        $new_user_2 = User::find(4);

        // Requesting the api while the store have an owner
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/stores/1/owner/users/'.$new_user_2->id.'/');
        $response->assertStatus(400)
                 ->assertJson(['message'=> 'This store already have an owner']);
    }

    public function test_assigning_store_ownership_with_admin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->postJson('/api/stores/1/owner/users/2/');
        $response->assertForbidden();
    }

    public function test_assigning_store_ownership_with_unauthorized_user(): void
    {
        $response = $this->postJson('/api/stores/1/owner/users/2/');
        $response->assertUnauthorized();
    }

    public function test_assigning_store_ownership_with_nonexisting_store_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/stores/10/owner/users/2/');
        $response->assertNotFound();
    }

    public function test_assigning_store_ownership_with_nonexisting_user_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/stores/1/owner/users/10/');
        $response->assertNotFound();
    }

    public function test_deleting_store_ownership_with_superadmin_permission(): void
    {
        // Create a new user to be the owner of store 1
        User::factory()->count(1)->create();
        $user = User::find(3);
        $user->store_id = 1;
        $user->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/stores/1/owner/');
        $store = new StoreResource(Store::find(1));
        $response->assertOk()
                 ->assertJson($store->jsonSerialize());

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/stores/1/owner/');
        $response->assertStatus(400)
                 ->assertJson(['message'=> 'This store currently not having any owner']);
    }

    public function test_deleting_store_ownership_with_admin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->deleteJson('/api/stores/1/owner/');
        $response->assertForbidden();
    }

    public function test_deleting_store_ownership_with_unauthorized_user(): void
    {
        $response = $this->deleteJson('/api/stores/1/owner/');
        $response->assertUnauthorized();
    }

    public function test_deleting_store_ownership_with_nonexisting_store_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/stores/10/owner/');
        $response->assertNotFound();
    }
}
