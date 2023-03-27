<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

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

        $this->assertEquals(Booking::count(), 0);
        
        $response->assertStatus(406)
                ->assertExactJson([
                    'message' => 'Nem megfelelő telefonszám.'
                ]);
    }

    /**
     * Test booking date interval is valid.
     */
    public function test_input_date_intval_validation(): void
    {
        $this->withoutExceptionHandling();
        
        $response = $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-03',
            'to' => '2023-01-01',
            'room_id' => 2
        ]);

        $this->assertEquals(Booking::count(), 0);
        
        $response->assertStatus(406)
                ->assertExactJson([
                    'message' => 'Nem megfelelő foglalási dátumok.'
                ]);
    }

    /**
     * Test there is not any booking with them same email and default status
     */
    public function test_there_is_not_default(): void
    {
        $this->withoutExceptionHandling();
        
        $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-01',
            'to' => '2023-01-03',
            'room_id' => 2
        ]);

        $response = $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-01',
            'to' => '2023-01-03',
            'room_id' => 3
        ]);

        $this->assertEquals(Booking::count(), 1);
        
        $response->assertStatus(406)
                ->assertExactJson([
                    'message' => 'Már van jóváhagyásra váró foglalása.'
                ]);
    }

    /**
     * Test there is not any bookings on the selected room in the selected date interval
     */
    public function test_there_is_not_any_bookings(): void
    {
        $this->withoutExceptionHandling();
        
        $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-01',
            'to' => '2023-01-03',
            'room_id' => 2
        ]);

        $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test1@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-06',
            'to' => '2023-01-09',
            'room_id' => 2
        ]);

        $response = $this->postJson('/api/booking/create', [
            'name' => 'John Doe',
            'email' => 'test2@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-02',
            'to' => '2023-01-07',
            'room_id' => 2
        ]);

        $this->assertEquals(Booking::count(), 2);
        
        $response->assertStatus(406)
                ->assertExactJson([
                    'message' => 'Erre a szobára már van rögzített foglalás az Ön által választott időpontban.'
                ]);

                $response2 = $this->postJson('/api/booking/create', [
                    'name' => 'John Doe',
                    'email' => 'test2@john.com',
                    'phone' => '06201112233',
                    'from' => '2023-01-04',
                    'to' => '2023-01-05',
                    'room_id' => 2
                ]);

                $this->assertEquals(Booking::count(), 3);
        
                $response2->assertStatus(201);
    }

    /**
     * A booking can be accepted by administrator.
     */
    public function test_a_booking_can_be_accepted_by_admin(): void
    {
        //$this->withoutExceptionHandling();
        
        $booking = Booking::create([
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-01',
            'to' => '2023-01-03',
            'room_id' => 2,
            'status' => 'jóváhagyásra vár'
        ]);

        $response1 = $this->get('/api/booking/accept/'.$booking->id);

        $updatedBooking1 = Booking::find($booking->id);

        
        $this->assertEquals($updatedBooking1->status, 'jóváhagyásra vár');
        $this->assertGuest();

        Sanctum::actingAs(
            User::factory()->create(['role' => 2]),
            ['*']
        );

        $response2 = $this->get('/api/booking/accept/'.$booking->id);

        $updatedBooking2 = Booking::find($booking->id);

        
        $this->assertEquals($updatedBooking2->status, 'jóváhagyásra vár');
        $this->assertAuthenticated();
        $response2->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthorized'
            ]);
        
        Sanctum::actingAs(
            User::factory()->create(['role' => 1]),
            ['*']
        );

        $response = $this->get('/api/booking/accept/'.$booking->id);

        $updatedBooking = Booking::find($booking->id);

        $this->assertEquals(Booking::count(), 1);
        $this->assertEquals($updatedBooking->name, 'John Doe');
        $this->assertEquals($updatedBooking->email, 'test@john.com');
        $this->assertEquals($updatedBooking->phone, '06201112233');
        $this->assertEquals($updatedBooking->from, '2023-01-01');
        $this->assertEquals($updatedBooking->to, '2023-01-03');
        $this->assertEquals($updatedBooking->room_id, 2);
        $this->assertEquals($updatedBooking->status, 'jóváhagyva');

        $this->assertAuthenticated();
        $response->assertStatus(201)
                ->assertExactJson([
                    'message' => 'Foglalás jóváhagyva.'
                ]);

    }

    /**
     * A booking can be rejected.
     */
    public function test_a_booking_can_be_rejected(): void
    {
        $this->withoutExceptionHandling();
        
        $booking = Booking::create([
            'name' => 'John Doe',
            'email' => 'test@john.com',
            'phone' => '06201112233',
            'from' => '2023-01-01',
            'to' => '2023-01-03',
            'room_id' => 2,
            'status' => 'jóváhagyásra vár'
        ]);

        $response = $this->get('/api/booking/reject/'.$booking->id);

        $updatedBooking = Booking::find($booking->id);

        $this->assertEquals(Booking::count(), 1);
        $this->assertEquals($updatedBooking->name, 'John Doe');
        $this->assertEquals($updatedBooking->email, 'test@john.com');
        $this->assertEquals($updatedBooking->phone, '06201112233');
        $this->assertEquals($updatedBooking->from, '2023-01-01');
        $this->assertEquals($updatedBooking->to, '2023-01-03');
        $this->assertEquals($updatedBooking->room_id, 2);
        $this->assertEquals($updatedBooking->status, 'elutasítva');

        $response->assertStatus(201)
                ->assertExactJson([
                    'message' => 'Foglalás elutasítva.'
                ]);
    }
}
