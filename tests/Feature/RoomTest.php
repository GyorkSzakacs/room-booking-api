<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Room;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Someone can get the list of bookable rooms.
     */
    public function test_get_rooms(): void
    {
        
        $this->WithoutExceptionHandling();

        Room::create([
            'name' => 'Room1'
        ]);
        Room::create([
            'name' => 'Room2'
        ]);
        Room::create([
            'name' => 'Room3'
        ]);
        Room::create([
            'name' => 'Room4'
        ]);
        Room::create([
            'name' => 'Room5'
        ]);

        $response = $this->get('/api');

        $response
            ->assertStatus(201)
            ->assertJson([
            [
                'id' => 1,
                'name' => 'Room1'
            ],
            [
                'id' => 2,
                'name' => 'Room2'
            ],
            [
                'id' => 3,
                'name' => 'Room3'
            ],
            [
                'id' => 4,
                'name' => 'Room4'
            ],
            [
                'id' => 5,
                'name' => 'Room5'
            ]
        ]);
    }
}
