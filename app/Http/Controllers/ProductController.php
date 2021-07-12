<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CategoryProduct;
use App\Models\Category;
use App\Models\Store;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->is_superadmin){
            return response()->json(ProductResource::collection(Product::all()), 200);
        }
        
        $store = Store::find($request->user()->store_id);
        if ($store){
            $products = Product::where('store_id', $request->user()->store_id);
            return response()->json(ProductResource::collection($products), 200);
        }

        return response()->json(['message'=> 'An admin can only view product that are available on their store, and you are not owning any store.'], 400);
    }

    public function create(Request $request, $store_id)
    {
        $store = Store::findOrFail($store_id);

        if ($request->user()->is_superadmin || $store_id == $request->user()->store_id){
            $product = Product::create($request->all());
            $product->store_id = $store_id;
            $product->save();

            return response()->json(new ProductResource($product->refresh()), 201);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function show(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->user()->is_superadmin || $product->store_id == $request->user()->store_id){
            return response()->json(new ProductResource($product), 200);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->user()->is_superadmin || $request->user()->store_id == $product->store_id){
            $product->update($request->all());

            return response()->json(new ProductResource($product->refresh()), 200);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function destroy(Request $request, $id)
    {   
        $product = Product::findOrFail($id);

        if ($request->user()->is_superadmin || $request->user()->store_id == $product->store_id){
            $product->delete();
            return response()->json(NULL, 204);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function addCategory(Request $request, $id, $category_id)
    {
        $product = Product::findOrFail($id);

        if ($request->user()->is_superadmin || $request->user()->store_id == $product->store_id){
            $category = Category::findOrFail($category_id);
            $payload = [
                'product_id'=> $product->id,
                'category_id'=> $category->id
            ];

            $exists = CategoryProduct::find($payload);
            if ($exists){
                CategoryProduct::create($payload);
                return response()->json(new ProductResource($product->refresh()));
            }
            
            return response()->json(['message'=> 'This product already have this category'], 400);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function removeCategory(Request $request, $id, $category_id)
    {
        $product = Product::findOrFail($id);

        if ($request->user()->is_superadmin || $request->user()->store_id == $product->store_id){
            $category = Category::findOrFail($category_id);
            $payload = [
                'product_id'=> $product->id,
                'category_id'=> $category->id
            ];

            $exists = CategoryProduct::find($payload);
            if ($exists){
                CategoryProduct::where($payload)->first()->delete();
                return response()->json(new ProductResource($product->refresh()));
            }
            
            return response()->json(['message'=> 'This product did not have this category'], 400);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }
}
