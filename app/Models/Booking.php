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
     * Get default value of the booking status
     * 
     * @return sting
     */
    public static function getDefaultStatus(){
        return self::$defaultStatus;
    }
}
