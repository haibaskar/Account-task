<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Helpers\LuhnGenerator;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{

    
    /**
     * Create a new account.
     *
     * @return user
     */
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'account_name' => 'required|string|unique:users,account_name',
            'account_type' => ['required'],
            'currency' => ['required'],
            'password' => ['required'],
        ]);

        if($validated->fails()){
            $response = [
                'error'    => $validated->errors(),
            ];
            return response()->json($response);
        }

        $account_number = LuhnGenerator::generate();
        $account = User::create([
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'account_name' => $request['account_name'],
            'account_type' => $request['account_type'],
            'currency' => $request['currency'],
            'account_number' => $account_number,
            'password' => Hash::make($request['password']),
            'balance' => 0.00,
        ]);

        return response()->json(['message' => 'Account created', 'data' => $account], 201);
    }
    
    /**
     * Login to user account.
     *
     * @return user and token
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'account_number' => 'required|exists:users,account_number',
            'password' => 'required'
        ]);

        $user = User::where('account_number', $request->account_number)->first();
        if (!$user || $user->deleted_at) {
            return response()->json(['message' => 'User not found or deactivated'], 403);
        }

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

}
