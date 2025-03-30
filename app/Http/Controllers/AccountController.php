<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Secure routes
    }

   
    /**
     * Fetch current user account details.
     *
     * @return user details
     */
    public function profileData(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Token mismatch or invalid'], 401);
        }

        return response()->json(['user' => $user]);
    }

    /**
     * Update account details.
     *
     * @return user details
     */
    public function update(Request $request,$account_number)
    {
        $validated = Validator::make($request->all(), [
            'account_name' => 'required|string',
            'account_type' => ['required'],
            'currency' => ['required'],
        ]);
    
        if($validated->fails()){
            $response = [
                'error'    => $validated->errors(),
            ];
            return response()->json($response);
        }

        $account = User::where('account_number', $account_number)
                 ->firstOrFail();
        $account->account_name= $request['account_name'];
        $account->account_type = $request['account_type'];
        $account->currency = $request['currency'];
        $account->update();

        return response()->json(['message' => 'Account updated', 'data' => $account]);
    }
    
    /**
     * Deactivate (soft delete) account
     */
    public function destroy($account_number)
    {
        $account = User::where('account_number', $account_number)
            ->firstOrFail();

        $account->delete();
        return response()->json(['message' => 'Account deactivated']);
    }
}

