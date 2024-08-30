<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'birth' => 'required',
                'phone' => 'required',
                'picture' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $data = $request->all();
            $data['password'] = Hash::make($request->password);

            $picture = $request->file('picture');
            $picture->storeAs('public/profile', $picture->hashName());

            $data['picture'] = $picture->hashName();

            $user = User::create($data);

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Authenticated', 200);
        } catch (Exception $err) {
            return ResponseFormatter::error([
                'mesage' => 'Something Went Wrong',
                'error' => $err,
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password']
            ])) {
                return ResponseFormatter::error(['message' => 'Unautorized'], 'Authentication Failed', 500);
            }

            $user = User::where('email', $credentials['email'])->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something Went Wrong',
                    'error' => $error
                ],
                'Authentication Failed',
                500
            );
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success([
            $token,
            'Token Revoked'
        ]);
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:255',
                'picture' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $user = User::where('id', Auth::id())->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'Profile Not Found'
                ], 'Failed To Update Profile', 404);
            }

            if ($request->file('picture') == '') {
                $user->update([
                    'username' => $request->username
                ]);
            } else {
                Storage::disk('local')->delete('public/profile/' . basename($user->picture));

                $picture = $request->file('picture');
                $picture->storeAs('public/profile', $picture->hashName());

                $user->update([
                    'username' => $request->username,
                    'picture' => $picture->hashName()
                ]);
            }

            return ResponseFormatter::success($user, 'Data Profile Has Been Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Update Profile', 500);
        }
    }

    public function getUsers()
    {
        $users = User::where('role', 'user')->get();
        return ResponseFormatter::success($users, 'Data User Berhasil Diambil');
    }
}
