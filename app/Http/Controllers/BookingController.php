<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    
    /**
     * Create booking
     * 
     * @param Requiest $request
     * @return Response
     */
    public function create(Request $request){

        $request->validate([
            'name' => 'required | string',
            'email' => 'required | email',
            'phone' => 'required | string',
            'from' => 'required | date',
            'to' => 'required | date',
            'room_id' => 'required | integer'
        ]);

        if(!self::isValidPhone($request->phone)){
            return response()->json([
                'message' => 'Nem megfelelő telefonszám.'
            ], 406);
        }

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

    /**
     * Check a string for phone number validity.
     * 
     * @param string $phoneNumber
     * @return boolean
     */
    public static function isValidPhone($phoneNumber){

        if(!Str::startsWith($phoneNumber, '06')){
            return false;
        }

        if(!preg_match('/^[0-9]+$/', $phoneNumber)){
            return false;
        }

        return true;
    }
}
