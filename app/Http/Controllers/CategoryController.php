<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(CategoryResource::collection(Category::all()), 200);
    }

    public function create(Request $request)
    {
        if ($request->user()->is_superadmin == FALSE){
            return response()->json(['message'=> 'You do not have permission to do this action'], 403);
        }

        return response()->json(new CategoryResource(Category::create($request->all())), 201);
    }

    public function show(Request $request, $id)
    {
        return response()->json(new CategoryResource(Category::findOrFail($id)), 200);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->is_superadmin == FALSE){
            return response()->json(['message'=> 'You do not have permission to do this action'], 403);
        }
        
        $data = Category::findOrFail($id);
        $data->update($request->all());

        return response()->json(new CategoryResource(Category::findOrFail($id)), 200);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->is_superadmin == FALSE){
            return response()->json(['message'=> 'You do not have permission to do this action'], 403);
        }

        Category::findOrFail($id)->delete();

        return response()->json(NULL, 204);
    }
}
