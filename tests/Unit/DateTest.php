<?php

namespace Tests\Unit;

use App\Http\Controllers\BookingController;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * Test the given 'to' date is later then the 'from'.
     */
    public function test_date_intval_validation(): void
    {
        $validFrom = '2023-01-01';
        $validTo = '2023-01-08';

        $invalidFrom = '2023-01-08';
        $invalidTo = '2023-01-01';
        
        $this->assertTrue(BookingController::isValidDateInterval($validFrom, $validTo));
        $this->assertFalse(BookingController::isValidDateInterval($invalidFrom, $invalidTo));
    }
}
