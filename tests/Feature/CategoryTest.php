<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;

class CategoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Category::factory()->count(5)->create();

        $this->valid_create_category_payload = [
            'name'=> 'New Category',
            'description'=> 'This is a new category lmao'
        ];
    }

    public function test_get_all_categories(): void
    {
        $response = $this->getJson('/api/categories/');
        $response->assertOk();
    }

    public function test_get_a_category_detail(): void
    {
        $id = rand(1, 5);

        $response = $this->getJson('/api/categories/'.$id.'/');
        $response->assertOk();
    }

    public function test_get_a_category_detail_with_nonexisting_id(): void
    {
        $response = $this->getJson('/api/categories/10/');
        $response->assertNotFound();
    }

    public function test_create_a_category_with_superadmin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> $this->superadmin_token])
                         ->postJson('/api/categories/', $this->valid_create_category_payload);
        $response->assertCreated();

        $this->assertDatabaseHas('categories', [
            'name'=> $this->valid_create_category_payload['name'],
            'description'=> $this->valid_create_category_payload['description']
        ]);
    }

    public function test_create_a_category_with_admin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> $this->admin_token])
                         ->postJson('/api/categories/', $this->valid_create_category_payload);
        $response->assertForbidden();
    }

    public function test_create_a_category_with_unauthorized_user(): void
    {
        $response = $this->postJson('/api/categories/', $this->valid_create_category_payload);
        $response->assertUnauthorized();
    }

    public function test_update_a_category_with_superadmin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> $this->superadmin_token])
                         ->putJson('/api/categories/1/', $this->valid_create_category_payload);
        $response->assertOk();

        $this->assertDatabaseHas('categories', [
            'name'=> $this->valid_create_category_payload['name'],
            'description'=> $this->valid_create_category_payload['description']
        ]);
    }

    public function test_update_a_category_with_admin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> $this->admin_token])
                         ->putJson('/api/categories/1/', $this->valid_create_category_payload);
        $response->assertForbidden();
    }

    public function test_update_a_category_with_unauthorized_user(): void
    {
        $response = $this->putJson('/api/categories/1/', $this->valid_create_category_payload);
        $response->assertUnauthorized();
    }

    public function test_update_a_category_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> $this->superadmin_token])
                         ->putJson('/api/categories/10/', $this->valid_create_category_payload);
        $response->assertNotFound();
    }

    public function test_delete_a_category_with_superadmin_permission(): void
    {
        $category = Category::find(1);

        $response = $this->withHeaders(['Authorization'=> $this->superadmin_token])
                         ->deleteJson('/api/categories/1/');
        $response->assertNoContent();

        $this->assertDeleted($category);
    }

    public function test_delete_a_category_with_admin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> $this->admin_token])
                         ->deleteJson('/api/categories/1/');
        $response->assertForbidden();
    }

    public function test_delete_a_category_with_unauthorized_user(): void
    {
        $response = $this->deleteJson('/api/categories/1/');
        $response->assertUnauthorized();
    }

    public function test_delete_a_category_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> $this->superadmin_token])
                         ->deleteJson('/api/categories/10/', $this->valid_create_category_payload);
        $response->assertNotFound();
    }
}
