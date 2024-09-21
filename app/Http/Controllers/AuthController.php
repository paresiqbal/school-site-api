<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|unique:users,username|max:255', // Specify the table and column
            'password' => 'required',
        ]);


        $user = User::create($fields);

        $token = $user->createToken($request->username);

        return [
            'username' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request)
    {
        return 'login';
    }

    public function logout(Request $request)
    {
        return 'logout';
    }
}
