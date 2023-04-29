<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            
            $authUser = new UserResource(Auth::user()); 
            
            return response()->json([
                'user' => $authUser,
                'token' => $authUser->createToken('User-Token')->plainTextToken,
            ]);
        } 
        else{ 
            return response()->json([
                'status' => 422,
                'message' => "email or password is invalid"
            ], 422);
        } 

        
    }
}

