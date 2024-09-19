<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$user = User::paginate(10);
        $user = User::all();
        return response()->json([
            'success' => !!$user,
            'data' => $user
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id)->first();
        return response()->json([
            'success' => !!$user,
            'data' => $user
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $credentials = $request->only([
            'name', 'email', 'password'
        ]);

        $user = User::find($id);
        
        if(!$user) {
            throw ValidationException::withMessages([
                'user' => ['User not found']
            ]);
        }
        $update = [];
        if(isset($credentials['name']) && !empty($credentials['name']))
            $update['name'] = $credentials['name'];
        if(isset($credentials['email']) && !empty($credentials['email']))
            $update['email'] = $credentials['email'];
        if(isset($credentials['password']) && !empty($credentials['password']))
            $update['password'] = Hash::make($credentials['password']);

        if(!$update) {
            throw ValidationException::withMessages([
                'user' => ['Data incorrect!']
            ]);
        }
        $ok = $user->update($update);
        return response()->json([
            'success' => !!$ok,
            'data' => User::find($id)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if(!$user) {
            throw ValidationException::withMessages([
                'user' => ['User not found']
            ]);
        }
        $user->delete();
    }
}
