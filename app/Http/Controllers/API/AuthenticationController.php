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
     *
     * @return \Illuminate\Http\Response
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
}
