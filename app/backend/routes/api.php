<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


// Route::post('user_teste', [AuthController::class, 'userTeste']);
Route::post('login', [AuthController::class, 'auth']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', UserController::class);

    Route::get('/teste', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('/calendar', CalendarController::class);
});