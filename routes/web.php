<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GiveawayController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MembershipController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\CampaignCronController;
use App\Http\Controllers\Web\CronTriggerController;
use App\Http\Controllers\Web\SetupController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\SpinAndWinController;
use App\Http\Controllers\Web\UserMessageController;
use App\Http\Controllers\Web\WinnerDashboardController;
use App\Http\Controllers\Web\WinnerRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/setup', [SetupController::class, 'index'])->name('setup');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [WinnerDashboardController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/winner/lookup', [WinnerDashboardController::class, 'lookup'])->name('winner.lookup');
Route::post('/winner/login', [WinnerDashboardController::class, 'loginWithPassword'])->name('winner.login.password');
Route::post('/winner/claim', [WinnerDashboardController::class, 'claim'])->name('winner.claim');

Route::middleware('guest')->group(function () {
    Route::get('/register', [WinnerRegistrationController::class, 'showRegister'])->name('register');
    Route::post('/register', [WinnerRegistrationController::class, 'store']);
});

Route::middleware('winner.auth')->group(function () {
    Route::get('/winner/dashboard', [WinnerDashboardController::class, 'dashboard'])->name('winner.dashboard');
    Route::post('/winner/logout', [WinnerDashboardController::class, 'logout'])->name('winner.logout');
    Route::post('/winner/messages', [WinnerDashboardController::class, 'sendMessage'])->name('winner.messages.send');
    Route::post('/winner/messages/{message}/read', [WinnerDashboardController::class, 'markRead'])->name('winner.messages.read');
    Route::post('/winner/documents/upload', [WinnerDashboardController::class, 'uploadDocument'])->name('winner.documents.upload');
    Route::get('/winner/withdrawals', [WinnerDashboardController::class, 'showWithdrawals'])->name('winner.withdrawals');
    Route::post('/winner/withdrawals', [WinnerDashboardController::class, 'requestWithdrawal'])->name('winner.withdrawals.request');
    Route::get('/winner/deposits', [WinnerDashboardController::class, 'showDeposits'])->name('winner.deposits');
    Route::post('/winner/deposits', [WinnerDashboardController::class, 'submitDeposit'])->name('winner.deposits.submit');
    Route::get('/winner/transactions', [WinnerDashboardController::class, 'showTransactions'])->name('winner.transactions');
    Route::get('/winner/orders', [WinnerDashboardController::class, 'showOrders'])->name('winner.orders');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/messages', [UserMessageController::class, 'index'])->name('messages');
    Route::post('/messages', [UserMessageController::class, 'store']);
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/giveaways', [GiveawayController::class, 'index'])->name('giveaways');
Route::post('/giveaways/{giveaway}/enter', [GiveawayController::class, 'enter'])->name('giveaways.enter');

Route::get('/games', [SpinAndWinController::class, 'index'])->name('games');
Route::post('/games/spin', [SpinAndWinController::class, 'spin'])->name('spin.ajax');

Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::post('/shop/order', [ShopController::class, 'placeOrder'])->name('shop.order');

Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships');
Route::post('/memberships/signup', [MembershipController::class, 'signup'])->name('memberships.signup');

Route::get('/cron/trigger', [CronTriggerController::class, 'trigger'])->name('cron.trigger');
Route::get('/cron/send-campaign', [CampaignCronController::class, 'handle'])->name('cron.send-campaign');

Route::get('/winners/recent', [HomeController::class, 'recentWinners'])->name('winners.recent');
Route::get('/winners/stats', [HomeController::class, 'stats'])->name('winners.stats');
