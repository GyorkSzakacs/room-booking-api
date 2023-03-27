<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A user can be login.
     */
    public function test_login(): void
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create([
            'role' => 2
        ]);
        
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);


        $response->assertStatus(200);
        $this->assertAuthenticated();
    }
}
