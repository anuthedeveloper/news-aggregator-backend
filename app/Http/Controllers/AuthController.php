<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private function validate($data) 
    {
        $errors = [
            'email.unique' => 'This email is already registered. Please use a different email.',
        ];

        // Validate input data
        $validator = Validator::make($data->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], $errors);

        if ($validator->fails()) {
            throw new \Exception($validator->errors(), 1);
        }
    }

    public function register(Request $request)
    {
        // If validation passes, create the user
        try {
            $this->validate($request);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
    
            $token = JWTAuth::fromUser($user);
    
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(Request $request)
    {
        // Try to generate JWT token for the authenticated user
        try {
            // Validate the input fields
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // Check if the credentials are correct
            $credentials = $request->only('email', 'password');
            
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = JWTAuth::fromUser($user);
                // Return success response with the token
                return response()->json([
                    'token' => $token,
                ]);
            }

            throw new \Exception("Invalid credentials provided.", 1);
        } catch (\Exception $e) {
            // Return error response if token generation fails
            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function user()
    {
        if (Auth::check()) {
            return response()->json(Auth::user());
        }
    
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
    
    public function logout()
    {
        Auth::logout();
  
        return response()->json(['message' => 'Successfully logged out']);
    }
    
    // Refresh Token
    public function refresh(Request $request)
    {
        $token = JWTAuth::parseToken()->refresh();

        return response()->json([
            'token' => $token
        ]);
    }
  
}
