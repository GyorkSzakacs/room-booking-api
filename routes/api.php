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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Endpoint for getting the list of rooms.
Route::get('/', function () {
    $rooms =Room::all();;
    return response()->json(
        $rooms
    ,201);
});

//Endpoint for creation ofa booking.
Route::post('booking/create', [BookingController::class, 'create']);

//Endpoint for accept a booking.
Route::middleware('auth:sanctum')->get('booking/accept/{id}', [BookingController::class, 'accept']);

//Endpoint for reject a booking.
Route::middleware('auth:sanctum')->get('booking/reject/{id}', [BookingController::class, 'reject']);

//Endpoint for receive a user his/hor own bookingns
Route::middleware('auth:sanctum')->get('my-bookings', [BookingController::class, 'getUserBookings']);

//Endpoint for loing
Route::post('login', [UserController::class, 'login']);
