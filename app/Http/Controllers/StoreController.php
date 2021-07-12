<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;
use App\Http\Resources\StoreResource;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->is_superadmin){
            return response()->json(StoreResource::collection(Store::all()), 200);
        }
        
        $store = Store::find($request->user()->store_id);
        if ($store){
            return response()->json(new StoreResource($store), 200);
        }
        return response()->json(['message'=> $request->user()->email.' currently not owning any store, please ask a superadmin to assign one to you.'], 400);
    }

    public function create(Request $request)
    {
        if ($request->user()->is_superadmin == FALSE){
            return response()->json(['message'=> 'You do not have permission to do this action'], 403);
        }

        return response()->json(new StoreResource(Store::create($request->all())), 201);
    }

    public function show(Request $request, $id)
    {
        if ($request->user()->is_superadmin){
            return response()->json(new StoreResource(Store::findOrFail($id)), 200);
        }

        if ($request->user()->store_id == $id){
            $store = Store::find((int)$request->user()->store_id);
            if ($store){
                return response()->json(new StoreResource($store), 200);
            }
        }
        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->is_superadmin || $request->user()->store_id == $id){
            $data = Store::findOrFail($id);
            $data->update($request->all());

            return response()->json(new StoreResource(Store::findOrFail($id)), 200);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->is_superadmin || $request->user()->store_id == $id){
            Store::findOrFail($id)->delete();
            return response()->json(NULL, 204);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function assignOwner(Request $request, $id, $user_id)
    {
        if ($request->user()->is_superadmin){
            $store = Store::findOrFail($id);
            $store_owner = User::where('store_id', $id)->first();
            if($store_owner){
                return response()->json(['message'=> 'This store already have an owner'], 400);
            }

            $user = User::findOrFail($user_id);
            $user->store_id = $id;
            $user->save();

            return response()->json(new StoreResource($store->refresh()), 200);
        }

        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }

    public function unassignOwner(Request $request, $id)
    {
        if ($request->user()->is_superadmin){
            $store = Store::findOrFail($id);
            $store_owner = User::where('store_id', $id)->first();
            if($store_owner){
                $store_owner->store_id = NULL;
                $store_owner->save();
                
                return response()->json(new StoreResource($store->refresh()), 200);
            }
            return response()->json(['message'=> 'This store currently not having any owner'], 400);
        }
        return response()->json(['message'=> 'You do not have permission to do this action'], 403);
    }
}
