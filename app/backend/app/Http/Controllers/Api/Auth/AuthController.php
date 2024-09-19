<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    public function auth(Request $request) {

        $credentials = $request->only([
            'email', 'password', 'device_name'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrects']
            ]);
        }

        //if($request->has('logout_others_devices'))
        $user->tokens()->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token,
            'success' => !!$token
        ]);
    }

    public function userTeste(Request $request) {
        
        $credentials = $request->only([
            'name', 'email', 'password'
        ]);

        $ok = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password'])
        ]);
        return response()->json([
            'success' => !!$ok,
            'data' => $ok
        ]);
    }
}
