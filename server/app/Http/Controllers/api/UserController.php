<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request) {
        try {
            $request->validate($request, [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'birth' => 'required',
                'phone' => 'required',
                'picture' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $data = $request->all();

            $user = User::create($data);

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ;
        } catch (Exception $err) {}
    }

    public function login() {}

    public function updateProfile() {}

    public function getUsers() {}
}
