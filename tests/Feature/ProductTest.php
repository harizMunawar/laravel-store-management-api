<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Http\Resources\ProductResource;

class ProductTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Store::factory()->count(1)->create();
        Product::factory()->count(5)->create();

        $this->valid_create_product_payload = [
            'name'=> 'New Product',
            'description'=> 'This is a description somehow',
            'price'=> 20000,
            'stock'=> 5,
            'store_id'=> 1
        ];
    }

    public function test_get_all_products_with_superadmin_permission(): void
    {
        $products = ProductResource::collection(Product::all());

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->getJson('/api/products/');
        $response->assertOk();
    }

    // Important Fix
    // public function test_get_all_products_with_admin_permission_that_are_owning_store(): void
    // {
    //     $user = User::find(2);
    //     $user->store_id = 1;
    //     $user->save();

    //     $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
    //                      ->getJson('/api/products/');
    //     $response->assertStatus(200);
    // }

    public function test_get_all_products_with_admin_permission_that_are_not_owning_store(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->getJson('/api/products/');
        $response->assertStatus(400);
    }

    public function test_get_a_product_with_superadmin_permission(): void
    {
        $product = new ProductResource(Product::find(1));

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->getJson('/api/products/1/');
        $response->assertOk();
    }

    public function test_get_a_product_with_admin_permission(): void
    {
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->getJson('/api/products/1/');
        $response->assertForbidden();
    }

    public function test_get_a_product_with_owner_permission(): void
    {   
        $user = User::find(2);
        $user->store_id = 1;
        $user->save();
        Product::create($this->valid_create_product_payload);
        $product = new ProductResource(Product::find(6));

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->getJson('/api/products/6/');
        $response->assertOk();
    }

    public function test_get_a_product_with_unauthorized_user(): void
    {
        $response = $this->getJson('/api/products/1/');
        $response->assertUnauthorized();
    }

    public function test_get_a_product_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->getJson('/api/products/100/');
        $response->assertNotFound();
    }

    public function test_create_a_product_with_superadmin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/stores/1/products/', $this->valid_create_product_payload);
        $response->assertCreated();
        $this->assertDatabaseHas('products',['id'=> 6]);
    }

    public function test_create_a_product_with_admin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->postJson('/api/stores/1/products/', $this->valid_create_product_payload);
        $response->assertForbidden();
    }

    public function test_create_a_product_with_owner_permission(): void
    {
        $user = User::find(2);
        $user->store_id = 1;
        $user->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->postJson('/api/stores/1/products/', $this->valid_create_product_payload);
        $response->assertCreated();
        $this->assertDatabaseHas('products',['id'=> 6]);
    }

    public function test_create_a_product_with_unauthorized_user(): void
    {
        $response = $this->postJson('/api/stores/1/products/', $this->valid_create_product_payload);
        $response->assertUnauthorized();
    }

    public function test_create_a_product_with_nonexisting_store_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/stores/10/products/', $this->valid_create_product_payload);
        $response->assertNotFound();
    }

    public function test_update_a_product_with_superadmin_permission(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->putJson('/api/products/1/', $this->valid_create_product_payload);
        $response->assertOk();
        $this->assertDatabaseHas('products', $this->valid_create_product_payload);
    }

    public function test_update_a_product_with_admin_permission(): void
    {
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->putJson('/api/products/1/', $this->valid_create_product_payload);
        $response->assertForbidden();
    }

    public function test_update_a_product_with_owner_permission(): void
    {
        $user = User::find(2);
        $user->store_id = 1;
        $user->save();
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->putJson('/api/products/1/', $this->valid_create_product_payload);
        $response->assertOk();
        $this->assertDatabaseHas('products', $this->valid_create_product_payload);
    }

    public function test_update_a_product_with_unauthorized_user(): void
    {
        $response = $this->putJson('/api/products/1/', $this->valid_create_product_payload);
        $response->assertUnauthorized();
    }

    public function test_update_a_product_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->putJson('/api/products/100/', $this->valid_create_product_payload);
        $response->assertNotFound();
    }

    public function test_delete_a_product_with_superadmin_permission(): void
    {
        $product = Product::find(1);

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/products/1/');
        $response->assertNoContent();
        $this->assertDeleted($product);
    }

    public function test_delete_a_product_with_admin_permission(): void
    {
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->deleteJson('/api/products/1/');
        $response->assertForbidden();
    }

    public function test_delete_a_product_with_owner_permission(): void
    {
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();
        $user = User::find(2);
        $user->store_id = 1;
        $user->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->deleteJson('/api/products/1/');
        $response->assertNoContent();
        $this->assertDeleted($product);
    }

    public function test_delete_a_product_with_unauthorized_user(): void
    {
        $response = $this->deleteJson('/api/products/1/');
        $response->assertUnauthorized();
    }

    public function test_delete_a_product_with_nonexisting_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/products/100/');
        $response->assertNotFound();
    }

    public function test_add_a_category_with_superadmin_permission(): void
    {
        Category::factory()->count(1)->create();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/products/1/categories/1/');
        $response->assertOk();
        $this->assertDatabaseHas('category_product', [
            'product_id'=> 1,
            'category_id'=> 1
        ]);
    }

    public function test_add_a_category_with_admin_permission(): void
    {
        Category::factory()->count(1)->create();
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->postJson('/api/products/1/categories/1/');
        $response->assertForbidden();
    }

    public function test_add_a_category_with_owner_permission(): void
    {
        Category::factory()->count(1)->create();
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();
        $user = User::find(2);
        $user->store_id = 1;
        $user->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->postJson('/api/products/1/categories/1/');
        $response->assertOk();
        $this->assertDatabaseHas('category_product', [
            'product_id'=> 1,
            'category_id'=> 1
        ]);
    }

    public function test_add_a_category_with_unauthorized_user(): void
    {
        Category::factory()->count(1)->create();

        $response = $this->postJson('/api/products/1/categories/1/');
        $response->assertUnauthorized();
    }

    public function test_add_a_category_with_nonexisting_category_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/products/1/categories/1/');
        $response->assertNotFound();
    }

    public function test_add_a_category_with_nonexisting_product_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->postJson('/api/products/100/categories/1/');
        $response->assertNotFound();
    }

    public function test_remove_a_category_with_superadmin_permission(): void
    {
        Category::factory()->count(1)->create();
        CategoryProduct::create(['product_id'=> 1, 'category_id'=> 1]);

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/products/1/categories/1/');
        $response->assertOk();
        $this->assertDatabaseMissing('category_product', [
            'product_id'=> 1,
            'category_id'=> 1
        ]);
    }

    public function test_remove_a_category_with_admin_permission(): void
    {
        Category::factory()->count(1)->create();
        CategoryProduct::create(['product_id'=> 1, 'category_id'=> 1]);
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->deleteJson('/api/products/1/categories/1/');
        $response->assertForbidden();
    }

    public function test_remove_a_category_with_owner_permission(): void
    {
        Category::factory()->count(1)->create();
        CategoryProduct::create(['product_id'=> 1, 'category_id'=> 1]);
        $product = Product::find(1);
        $product->store_id = 1;
        $product->save();
        $user = User::find(2);
        $user->store_id = 1;
        $user->save();

        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->admin_token])
                         ->deleteJson('/api/products/1/categories/1/');
        $response->assertOk();
        $this->assertDatabaseMissing('category_product', [
            'product_id'=> 1,
            'category_id'=> 1
        ]);
    }

    public function test_remove_a_category_with_unauthorized_user(): void
    {
        Category::factory()->count(1)->create();

        $response = $this->deleteJson('/api/products/1/categories/1/');
        $response->assertUnauthorized();
    }

    public function test_remove_a_category_with_nonexisting_category_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/products/1/categories/1/');
        $response->assertNotFound();
    }

    public function test_remove_a_category_with_nonexisting_product_id(): void
    {
        $response = $this->withHeaders(['Authorization'=> 'Bearer '.$this->superadmin_token])
                         ->deleteJson('/api/products/100/categories/1/');
        $response->assertNotFound();
    }
}
