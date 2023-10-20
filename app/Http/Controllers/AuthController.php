<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function Register(Request $request) {
        $request->validate([
            "name" => "required|string",
            "phone" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:6",
        ]);

        $user = User::create([
            "name" => $request->name,
            "phone" => $request->phone,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        $token = $user->createToken("Auth_Token")->plainTextToken;

        return response()->json([
            "message" => "Registration successful",
            "token" => $token
        ], Response::HTTP_CREATED);
    }
    function Login(Request $request) {
        $request->validate([
            "email" => "required|string",
            "password" => "required|string",
        ]);

        $user = User::firstWhere("email", $request->email);

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "Bad Credentials!"
            ], Response::HTTP_NOT_FOUND);
        }

        $token = $user->createToken("Auth_Token")->plainTextToken;

        return response()->json([
            "message" => "Success Login",
            "token" => $token
        ], Response::HTTP_OK);
    }
    public function updateUser(Request $request) {
        $user = auth()->user();

        $request->validate([
            "name" => "string",
            "phone" => "string",
            "email" => "email|unique:users,email," . $user->id, // Memungkinkan email yang sama kecuali untuk pengguna saat ini
            "password" => "string|min:6",
        ]);

        // Update data pengguna jika disediakan dalam request
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            "message" => "User information updated successfully",
            "user" => $user
        ], Response::HTTP_OK);
    }
    public function getUserInfo() {
        $user = auth()->user();
        // dd($user);

        return response()->json([
            "user" => $user
        ], Response::HTTP_OK);
    }
    function Logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            "message" => "Success Logout"
        ], Response::HTTP_OK);
    }
}
