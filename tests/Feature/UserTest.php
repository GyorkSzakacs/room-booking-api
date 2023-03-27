<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

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

    /**
     * An admin can set an other one.
     */
    public function test_admin_set_an_other(): void
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create([
            'role' => 2
        ]);
        
        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response1 = $this->get('/api/set-to-admin/'.$user->id);

        $response1->assertStatus(401);
        $this->assertEquals(User::first()->role, 2);
        
        $admin = User::factory()->create(['role' => 1]);
        
        Sanctum::actingAs(
            $admin,
            ['*']
        );
        
        $response2 = $this->get('/api/set-to-admin/'.$user->id);

        $response2->assertStatus(201);
        $this->assertEquals(User::first()->role, 1);
        
    }
}
