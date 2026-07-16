<?php

use App\Http\Controllers\Api\WinnerClaimController;
use App\Http\Controllers\Api\WinnerLookupController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::post('/winner/lookup', [WinnerLookupController::class, 'lookup']);
Route::post('/winner/claim', [WinnerClaimController::class, 'claim']);

Route::middleware('winner.auth')->group(function () {
    Route::get('/winner/dashboard', [WinnerClaimController::class, 'dashboard']);
    Route::get('/winner/messages', [MessageController::class, 'index']);
    Route::post('/winner/messages', [MessageController::class, 'store']);
    Route::post('/messages/{message}/read', [MessageController::class, 'markRead']);
});
