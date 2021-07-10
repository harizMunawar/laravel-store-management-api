<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            if(!$user){
                $response = ['message'=> 'No account is registered using '.$request->email];
            }
            else{
                $response = ['message'=> 'Incorrect password'];
            }
            return response()->json($response, 401);
        }
        
        $token = $user->createToken('appToken')->plainTextToken;
        $response = ['token'=> $token];
        return response()->json($response, 200);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        $response = ['message'=> 'Successfully logged out'];

        return response()->json($response, 200);
    }
}
