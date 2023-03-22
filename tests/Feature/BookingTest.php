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


    /**
     * Test required data validaton for booking
     */
    public function test_required_data(): void
    {
        //$this->withoutExceptionHandling();
        
        $response = $this->postJson('/api/booking/create', [
            'name' => '',
            'email' => '',
            'phone' => '',
            'from' => '',
            'to' => '',
            'room_id' => ''
        ]);

        $booking = Booking::first();

        $this->assertEquals(Booking::count(), 0);

        $response->assertInvalid('name');
        $response->assertInvalid('email');
        $response->assertInvalid('phone');
        $response->assertInvalid('from');
        $response->assertInvalid('to');
        $response->assertInvalid('room_id');
    }

    /**
     * Test required data type validaton for booking
     */
    public function test_data_types(): void
    {
        //$this->withoutExceptionHandling();
        
        $response = $this->postJson('/api/booking/create', [
            'name' => 2,
            'email' => 'bedAddress',
            'phone' => true,
            'from' => 'bedDate',
            'to' => 'bedDate',
            'room_id' => 'bedId'
        ]);

        $booking = Booking::first();

        $this->assertEquals(Booking::count(), 0);

        $response->assertInvalid('name');
        $response->assertInvalid('email');
        $response->assertInvalid('phone');
        $response->assertInvalid('from');
        $response->assertInvalid('to');
        $response->assertInvalid('room_id');
    }

    /**
     * Test valid phone number input data.
     */
    public function test_phone_format(): void
    {
        $this->withoutExceptionHandling();
        
        $response = $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '30201112233',
            'from' => '2023-01-01',
            'to' => '2023-01-03',
            'room_id' => 2
        ]);

        $booking = Booking::first();

        $this->assertEquals(Booking::count(), 0);
        
        $response->assertStatus(406)
                ->assertExactJson([
                    'message' => 'Nem megfelelő telefonszám.'
                ]);
    }
}
