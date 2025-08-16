<?php

use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;

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


Route::prefix('auth')->group(
    function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/reset-password', [AuthController::class, 'reset-password'])->name('reset-password');
    }
);

Route::middleware(['auth:sanctum'])
    ->group(
        function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('/otp/request', [AuthController::class, 'sendOtp'])->name('send-otp');
            Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('verify-otp');
            Route::post('/new-password', [AuthController::class, 'verify-otp'])->name('new-password');
            Route::get('/user/profile', [UserController::class, 'getUserProfile'])->name('user.profile');
            Route::post('/user/profile', [UserController::class, 'update_user_profile'])->name('user.profile.update');
        }
    );

Route::middleware(['auth:sanctum', 'tenant'])
    ->prefix('wallet')
    ->group(
        function () {
            Route::get('/balance', [\App\Http\Controllers\WalletController::class, 'getBalance'])->name('wallet.balance');
            Route::post('/withdraw', [\App\Http\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');
            Route::get('/history', [\App\Http\Controllers\WalletController::class, 'history'])->name('wallet.transactions');
        }
    );

Route::middleware(['auth:sanctum', 'tenant'])
    ->prefix('applets')
    ->group(
        function () {
            Route::get('/', [\App\Http\Controllers\AppletController::class, 'index'])->name('applets.index');
            Route::get('/active', [\App\Http\Controllers\AppletController::class, 'active'])->name('applets.active');
            Route::get('/{applet_id}/tasks', [\App\Http\Controllers\TaskController::class, 'index'])->name('applets.tasks.index');
            Route::get('/{applet_id}/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('applets.tasks.show');
            Route::post('/{applet}/tasks/{task}/start', [\App\Http\Controllers\TaskController::class, 'start_task'])->name('applets.tasks.update');
            Route::post('/{applet}/tasks/{task}/complete', [\App\Http\Controllers\TaskController::class, 'stop_task'])->name('applets.tasks.stop');
        }
    );

Route::middleware(['auth:sanctum'])->prefix('support')->group(function () {
    Route::get('tickets', [TicketController::class, 'index']);
    Route::post('tickets', [TicketController::class, 'store']);
    Route::get('tickets/{id}', [TicketController::class, 'show']);
    Route::post('tickets/{id}/reply', [TicketController::class, 'reply']);
});
