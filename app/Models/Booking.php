<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The guarded attributes.
     * 
     * @var array
     */
    protected $guarded = [];

    /**
     * The default value of the booking status.
     * 
     * @var string
     */
    protected static $defaultStatus = 'jóváhagyásra vár';

    /**
     * The value of the accepted booking status.
     * 
     * @var string
     */
    protected static $acceptedStatus = 'jóváhagyva';

    /**
     * The value of the rejected booking status.
     * 
     * @var string
     */
    protected static $rejectedStatus = 'elutasítva';

    /**
     * Get default value of the booking status
     * 
     * @return sting
     */
    public static function getDefaultStatus(){
        return self::$defaultStatus;
    }

    /**
     * Get accepted status value.
     * 
     * @return sting
     */
    public static function getAcceptedStatus(){
        return self::$acceptedStatus;
    }

    /**
     * Get rejected status value
     * 
     * @return sting
     */
    public static function getRejectedStatus(){
        return self::$rejectedStatus;
    }
}
