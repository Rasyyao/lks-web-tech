<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRedirectAndErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function login(string $username): void
    {
        $this->post('/keluar');
        $this->post('/masuk', ['username' => $username, 'password' => 'password123']);
    }

    public function test_mentor_lands_on_the_submission_inbox_after_login(): void
    {
        $this->post('/masuk', ['username' => 'mentor1', 'password' => 'password123'])
            ->assertRedirect('/mentor/pengumpulan');
    }

    public function test_admin_lands_on_the_admin_panel_after_login(): void
    {
        $this->post('/masuk', ['username' => 'admin', 'password' => 'password123'])
            ->assertRedirect(url('/admin'));
    }

    public function test_mentor_is_not_sent_back_to_a_forbidden_intended_admin_url(): void
    {
        $this->withSession(['url.intended' => url('/admin')])
            ->post('/masuk', ['username' => 'mentor1', 'password' => 'password123'])
            ->assertRedirect('/mentor/pengumpulan');
    }

    public function test_student_is_not_sent_back_to_a_forbidden_intended_mentor_url(): void
    {
        $this->withSession(['url.intended' => url('/mentor/pengumpulan')])
            ->post('/masuk', ['username' => '541221001', 'password' => 'password123'])
            ->assertRedirect('/beranda');
    }

    public function test_an_allowed_intended_url_is_still_honoured(): void
    {
        $this->withSession(['url.intended' => url('/peringkat')])
            ->post('/masuk', ['username' => '541221001', 'password' => 'password123'])
            ->assertRedirect(url('/peringkat'));
    }

    public function test_mentor_is_not_sent_to_beranda_from_intended_url(): void
    {
        $this->withSession(['url.intended' => url('/beranda')])
            ->post('/masuk', ['username' => 'mentor1', 'password' => 'password123'])
            ->assertRedirect('/mentor/pengumpulan');
    }

    public function test_mentor_is_not_sent_to_root_from_intended_url(): void
    {
        $this->withSession(['url.intended' => url('/')])
            ->post('/masuk', ['username' => 'mentor1', 'password' => 'password123'])
            ->assertRedirect('/mentor/pengumpulan');
    }

    public function test_root_redirects_each_role_to_its_own_home(): void
    {
        $this->login('mentor1');
        $this->get('/')->assertRedirect('/mentor/pengumpulan');

        $this->login('admin');
        $this->get('/')->assertRedirect(url('/admin'));

        $this->login('541221001');
        $this->get('/')->assertRedirect('/beranda');
    }

    public function test_mentor_visiting_beranda_is_redirected_to_mentor_inbox(): void
    {
        $this->login('mentor1');
        $this->get('/beranda')->assertRedirect('/mentor/pengumpulan');
    }

    public function test_admin_visiting_beranda_is_redirected_to_admin_panel(): void
    {
        $this->login('admin');
        $this->get('/beranda')->assertRedirect(url('/admin'));
    }

    public function test_mentor_opening_admin_sees_the_custom_forbidden_page(): void
    {
        $this->login('mentor1');

        $this->get('/admin')
            ->assertForbidden()
            ->assertSee('Akses ditolak')
            ->assertSee('Ke beranda saya')
            ->assertSee('/mentor/pengumpulan');
    }

    public function test_unknown_page_shows_the_custom_not_found_page(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('Masuk');
    }
}
