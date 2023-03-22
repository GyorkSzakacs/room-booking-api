<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    
    public function create(Request $request){

        $request->validate([
            'name' => 'required | string',
            'email' => 'required | email',
            'phone' => 'required | string',
            'from' => 'required | date',
            'to' => 'required | date',
            'room_id' => 'required | integer'
        ]);

        Booking::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'from' => $request->from,
            'to' => $request->to,
            'room_id' => $request->room_id,
            'status' => 'jóváhagyásra vár'
        ]);

        return response()->json([
            'message' => 'Foglalási igényét rögzítettük. Kollégánk hamarosan felveszi Önnel a kapcsolatot.'
        ], 201);
    }
}
