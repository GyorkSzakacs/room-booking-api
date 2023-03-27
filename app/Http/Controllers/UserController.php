<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Login
     * 
     * @param Request $request
     * @return string
     */
    public function login(Request $request){

        $request->validate([
            'email' => 'required | string | email',
            'password' => 'required | string'
        ]);

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'status' => false,
                'message' => 'Helytelen felhasználónév vagy jelszó.',
            ], 401);
        }

        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'Sikeres bejelentkezés',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }
}
