<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if(Auth::check()){

            $request->validate([
                'from' => 'required | date',
                'to' => 'required | date',
                'room_id' => 'required | integer',
                'user_id' => 'required | integer'
            ]);

        }
        else{
            
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

            if(self::getDefaultBookings($request->email, Booking::getDefaultStatus())->count() > 0){
                return response()->json([
                    'message' => 'Már van jóváhagyásra váró foglalása.'
                ], 406);
            }

        }

        if(!self::isValidDateInterval($request->from, $request->to)){
            return response()->json([
                'message' => 'Nem megfelelő foglalási dátumok.'
            ], 406);
        }

        if(self::getBookingsAtSameInterval($request->from, $request->to, $request->room_id)->count() > 0){
            return response()->json([
                'message' => 'Erre a szobára már van rögzített foglalás az Ön által választott időpontban.'
            ], 406);
        }

        if(Auth::check()){

            Booking::create([
                'name' => null,
                'email' => null,
                'phone' => null,
                'from' => $request->from,
                'to' => $request->to,
                'room_id' => $request->room_id,
                'user_id' => $request->user()->id,
                'status' => Booking::getDefaultStatus()
            ]);

        }
        else{

            Booking::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'from' => $request->from,
                'to' => $request->to,
                'room_id' => $request->room_id,
                'user_id' => null,
                'status' => Booking::getDefaultStatus()
            ]);

        }

        return response()->json([
            'message' => 'Foglalási igényét rögzítettük. Kollégánk hamarosan felveszi Önnel a kapcsolatot.'
        ], 201);
    }

    /**
     * Get the bookings of a user.
     * 
     * @param Request $request
     * @return string
     */
    public function getUserBookings(Request $request){

        $bookings = $request->user()->bookings;
        return response()->json(
            $bookings
        ,201);

    }

    /**
     * Get bookings.
     * 
     * @param Request $request
     * @return string
     */
    public function getBookings(Request $request){

        $user = $request->user();

        if(!$user->isAdmin()){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);;
        }

        $bookings = Booking::all();

        return response()->json(
            $bookings
        ,201);

    }

    /**
     * Accept booking
     * 
     * @param Requiest $request
     * @param int $id
     * @return Response
     */
    public function accept(Request $request, int $id){

        $user = $request->user();

        if(!$user->isAdmin()){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);;
        }

        $booking = Booking::find($id);
        $booking->status = Booking::getAcceptedStatus();
        $booking->save();

        return response()->json([
            'message' => 'Foglalás jóváhagyva.'
        ], 201);
    }

    /**
     * Reject booking
     * 
     * @param Requiest $request
     * @param int $id
     * @return Response
     */
    public function reject(Request $request, int $id){

        $user = $request->user();

        if(!$user->isAdmin()){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);;
        }
        
        $booking = Booking::find($id);
        $booking->status = Booking::getRejectedStatus();
        $booking->save();

        return response()->json([
            'message' => 'Foglalás elutasítva.'
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

    /**
     * Check the 'to' date is later then the 'from'
     * 
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public static function isValidDateInterval($from, $to){

        if($from > $to){
            return false;
        }

        return true;
    }

    /**
     * Get bookings with default status for an email
     * 
     * @param string $email
     * @param string $status
     * @return array
     */
    public static function getDefaultBookings($email, $status){

        return Booking::where([
            ['email', $email],
            ['status', $status]
        ])->get();
    }

    /**
     * Get bookings in the same intervel for a room.
     * 
     * @param string $from
     * @param string $to
     * @param int $roomId
     * @return array
     */
    public static function getBookingsAtSameInterval($from, $to, $roomId){

        return Booking::where([
            ['room_id', $roomId],
            ['from', '<', $to],
            ['to', '>', $from]
        ])->get();
    }
}
