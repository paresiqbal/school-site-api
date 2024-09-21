<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|unique:users,username|max:255',
            'password' => 'required',
        ]);

        $fields['password'] = Hash::make($fields['password']);
        $user = User::create($fields);
        $token = $user->createToken($user->username);

        return [
            'username' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['message' => 'Provided credentials are incorrect'];
        }

        $token = $user->createToken($user->username);

        return [
            'username' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return ['message' => 'Your are logged out'];
    }
}
