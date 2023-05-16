<?php

namespace App\Http\Controllers;

use App\Custom\Jwt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\Authenticate;

class LoginController extends Controller
{
    // LOGIN FUNCTION THAT I USED TO COMMUNICATE WITH POSTMAN 

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('username', 'password');

    //     if (Auth::attempt($credentials)) {
    //         // Authentication successful
    //         $user = Auth::user();
    //         $token = $user->createToken('token-name')->plainTextToken;

    //         return response()->json(['token' => $token]);
    //     } else {
    //         // Authentication failed
    //         return response()->json(['error' => 'Username or Password is incorrect'], 401);
    //     }
    // }

    // LOGIN FUNCTION THAT I USE TO COMMUNICATE WITH THE FRONTEND

    public function login(Request $request){
        $user = User::where('username', $request->username)->first();

        if(!$user) {
           return response()->json('User not found!', 401);
        }

        if(!password_verify($request->password, $user->password)){
           return response()->json('Password is Invalid!', 401);
        }

        $token = Jwt::create($user);

        return response()->json([
            'token' => $token,
            'user' => [
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'role' => $user->role->name
            ]
        ]);
    }

    public function verify(){
        return Jwt::validate();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        // $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
