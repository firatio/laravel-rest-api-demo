<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    /**
     * Register user
     */
    public function register(Request $request)
    {
        try {
            // Validate information
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8']
            ]);
		
            // Create user
            $user = User::create([
                'name' => $request->email,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'id' => $user->id
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            // Validate information
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8']
            ]);

            // Try to authenticate user
            if (!auth()->attempt($request->all())) {
                return response()->json([
                    'error' => 'Login information is invalid'
                ], 401);
            }

            // Create access token and send it
            $token = auth()->user()->createToken('authToken');

            return response()->json([
                'token' => $token->plainTextToken
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            // Delete all tokens
            auth()->user()->tokens()->delete();

            return response()->noContent();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    /**
     * Send information about the current user
     */
    public function show(Request $request)
    {
        try {
            // Respond only with email address
            return response()->json([
                'email' => auth()->user()->email
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }
}
