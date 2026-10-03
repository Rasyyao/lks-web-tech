<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/masuk');

        $response->assertStatus(200);
        $response->assertSee('Username (NIS)');
        $response->assertSee('Password');
    }

    public function test_student_can_authenticate_using_username_and_password(): void
    {
        $response = $this->post('/masuk', [
            'username' => '541221001',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/beranda');
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::where('username', '541221001')->first();
        $user->update(['is_active' => false]);

        $response = $this->post('/masuk', [
            'username' => '541221001',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_user_with_must_change_password_is_redirected_to_password_change(): void
    {
        // Putri has must_change_password = true
        $response = $this->post('/masuk', [
            'username' => '541221004',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/profil/password');
    }

    public function test_wrong_credentials_show_generic_error(): void
    {
        $response = $this->post('/masuk', [
            'username' => '541221001',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_login_is_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/masuk', [
                'username' => '541221001',
                'password' => 'wrong',
            ]);
        }

        // 6th attempt should be throttled (HTTP 429)
        $response = $this->post('/masuk', [
            'username' => '541221001',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }

    public function test_user_can_logout(): void
    {
        $dewi = User::where('username', '541221001')->first();

        $response = $this->actingAs($dewi)->post('/keluar');

        $this->assertGuest();
        $response->assertRedirect('/masuk');
    }
}
