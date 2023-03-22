<?php

namespace Tests\Unit;

use App\Http\Controllers\BookingController;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    /**
     * Test a phone number is valid.
     */
    public function test_phone_number_is_valid(): void
    {
        $validNumber = '06201112233';
        $invalidNumber1 = '3011112233';
        $invalidNumber2 = '0620j11k2233';
        
        $this->assertTrue(BookingController::isValidPhone($validNumber));
        $this->assertFalse(BookingController::isValidPhone($invalidNumber1));
        $this->assertFalse(BookingController::isValidPhone($invalidNumber2));
    }
}
