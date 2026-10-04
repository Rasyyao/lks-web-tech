<?php

namespace Tests\Feature;

use App\Livewire\Shared\Profile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_profile_password_page_renders_with_show_password_toggle(): void
    {
        $user = User::where('username', '541221004')->firstOrFail();

        $this->actingAs($user)
            ->get('/profil/password')
            ->assertOk()
            ->assertSee('Ubah password')
            ->assertSee('Password baru')
            ->assertSee('Konfirmasi password baru')
            ->assertSee('Sembunyikan password');
    }

    public function test_password_update_requires_uppercase_symbol_and_minimum_8_characters(): void
    {
        $user = User::where('username', '541221004')->firstOrFail();

        // 1. Less than 8 characters
        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password', 'Pass1!')
            ->set('password_confirmation', 'Pass1!')
            ->call('updatePassword')
            ->assertHasErrors(['password']);

        // 2. 8 characters without uppercase
        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password', 'password123!')
            ->set('password_confirmation', 'password123!')
            ->call('updatePassword')
            ->assertHasErrors(['password']);

        // 3. 8 characters without symbol
        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password', 'Password123')
            ->set('password_confirmation', 'Password123')
            ->call('updatePassword')
            ->assertHasErrors(['password']);

        // 4. Valid password with 8+ chars, uppercase, lowercase, and special symbol
        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password', 'Rahasia123!')
            ->set('password_confirmation', 'Rahasia123!')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertTrue(Hash::check('Rahasia123!', $user->password));
        $this->assertFalse((bool) $user->must_change_password);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.password_changed',
            'subject_id' => $user->id,
        ]);
    }

    public function test_user_does_not_need_to_input_current_password(): void
    {
        // Dewi has must_change_password = false
        $dewi = User::where('username', '541221001')->firstOrFail();
        $this->assertFalse((bool) $dewi->must_change_password);

        // The current password field is not shown on the page
        $this->actingAs($dewi)
            ->get('/profil/password')
            ->assertOk()
            ->assertDontSee('Password saat ini');

        // Dewi can change password without providing current_password
        Livewire::actingAs($dewi)
            ->test(Profile::class)
            ->set('password', 'BaruTelkom2026@')
            ->set('password_confirmation', 'BaruTelkom2026@')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $dewi->refresh();
        $this->assertTrue(Hash::check('BaruTelkom2026@', $dewi->password));
    }
}
