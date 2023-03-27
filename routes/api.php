<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Room;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;

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

//Endpoint for getting the list of rooms.
Route::get('/', function () {
    $rooms =Room::all();;
    return response()->json(
        $rooms
    ,201);
});

//Endpoint for creation ofa booking.
Route::post('booking/create', [BookingController::class, 'create']);

//Endpoint for loing
Route::post('login', [UserController::class, 'login']);

//Endpoint for authenticated users.
Route::group(['middleware' => 'auth:sanctum'], function (){
    Route::get('bookings', [BookingController::class, 'getBookings']);
    Route::get('booking/accept/{id}', [BookingController::class, 'accept']);
    Route::get('booking/reject/{id}', [BookingController::class, 'reject']);
    Route::get('set-to-admin/{id}', [UserController::class, 'setToAdmin']);
    Route::get('my-bookings', [BookingController::class, 'getUserBookings']);
});
