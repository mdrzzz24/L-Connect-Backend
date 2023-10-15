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
    function Logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            "message" => "Success Logout"
        ], Response::HTTP_OK);
    }
}
