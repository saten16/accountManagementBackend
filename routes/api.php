<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/ping', [ApiController::class, 'ping']);
Route::post('/amount', [ApiController::class, 'amount']);
Route::get('/transaction/{transaction_id}', [ApiController::class, 'get_transaction_data']);
Route::get('/balance/{account_id}', [ApiController::class, 'get_account_balance']);
Route::get('/max_transaction_volume', [ApiController::class, 'max_transaction_volume']);