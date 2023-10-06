<?php

use App\Http\Controllers\RedemptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::controller(RedemptionController::class)->group(function () {

    Route::post('/redeem_code', 'redeemCode')->name('redeem_code');

});
