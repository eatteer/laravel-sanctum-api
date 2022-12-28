<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password'])
        ]);

        $token = $user->createToken('pantsumarumie')->plainTextToken;

        return ['token' => $token];
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response(['message' => 'Invalid credentials'], 400);
        }

        $token = $user->createToken('pantsumarumie')->plainTextToken;

        return ['token' => $token];
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return ['message' => 'Logged out.'];
    }
}
