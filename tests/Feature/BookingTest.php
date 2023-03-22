<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Booking;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A booking can be crated.
     */
    public function test_a_booking_can_be_created(): void
    {
        $this->withoutExceptionHandling();
        
        $response = $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-01',
            'to' => '2023-01-03',
            'room_id' => 2
        ]);

        $booking = Booking::first();

        $this->assertEquals(Booking::count(), 1);
        $this->assertEquals($booking->name, 'John Doe');
        $this->assertEquals($booking->email, 'test@john.com');
        $this->assertEquals($booking->phone, '06201112233');
        $this->assertEquals($booking->from, '2023-01-01');
        $this->assertEquals($booking->to, '2023-01-03');
        $this->assertEquals($booking->room_id, 2);
        $this->assertEquals($booking->status, 'jóváhagyásra vár');

        $response->assertStatus(201)
                ->assertExactJson([
                    'message' => 'Foglalási igényét rögzítettük. Kollégánk hamarosan felveszi Önnel a kapcsolatot.'
                ]);
    }
}
