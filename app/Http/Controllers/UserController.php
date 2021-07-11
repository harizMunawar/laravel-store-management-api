<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(UserResource::collection(User::all()), 200);
    }

    public function store(Request $request)
    {
        if ($request->user()->is_superadmin == FALSE){
            return response()->json(['message'=> 'You do not have permission to do this action'], 403);
        }

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $data['is_superadmin'] = FALSE;

        return response()->json(new UserResource(User::create($data)), 201);        
    }

    public function show($id)
    {
        return response()->json(new UserResource(User::findOrFail($id)), 200);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->is_superadmin == FALSE){
            return response()->json(['message'=> 'You do not have permission to do this action'], 403);
        }

        $data = User::findOrFail($id);
        $data->update($request->except(['password']));

        return response()->json(new UserResource(User::findOrFail($id)), 200);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->is_superadmin == FALSE){
            return response()->json(['message'=> 'You do not have permission to do this action'], 403);
        }

        User::findOrFail($id)->delete();
        return response()->json(NULL, 204);
    }
}