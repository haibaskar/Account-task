<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
    Route::post('/accounts', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    });

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
       //Accounts Routes
       Route::get('/my-profile', [AccountController::class, 'profileData']);
       Route::put('/accounts/{account_number}', [AccountController::class, 'update']);
       Route::delete('/accounts/{account_number}', [AccountController::class, 'destroy']);

       // Transaction Routes
       Route::post('/transactions', [TransactionController::class, 'store']);
       Route::get('/transactions', [TransactionController::class, 'index']);
    });

