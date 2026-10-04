<?php

namespace Tests\Feature;

use App\Enums\ModuleTrack;
use App\Models\Topic;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapCategorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->student = User::where('username', '541221001')->firstOrFail();
    }

    public function test_material_index_renders_client_and_server_module_cards(): void
    {
        $this->actingAs($this->student)
            ->get('/materi')
            ->assertOk()
            ->assertSee('Modul')
            ->assertSee('Roadmap LKS Web Technologies')
            ->assertSee('Modul Client-Side')
            ->assertSee('Modul Server-Side')
            ->assertSee(route('materials.roadmap', 'client'))
            ->assertSee(route('materials.roadmap', 'server'));
    }

    public function test_client_roadmap_page_renders_client_topics_and_steps(): void
    {
        $this->actingAs($this->student)
            ->get('/materi/roadmap/client')
            ->assertOk()
            ->assertSee('Roadmap Pembelajaran: Modul Client-Side')
            ->assertSee('HTML5 Foundation & Web Semantics')
            ->assertSee('Modern CSS, Flexbox & Grid Layout')
            ->assertSee('Modern JavaScript (ES6+) & Core Logic')
            ->assertSee('JavaScript DOM Manipulation & Events')
            ->assertSee('Kembali ke Pilihan Modul');
    }

    public function test_server_roadmap_page_renders_server_topics_and_steps(): void
    {
        $this->actingAs($this->student)
            ->get('/materi/roadmap/server')
            ->assertOk()
            ->assertSee('Roadmap Pembelajaran: Modul Server-Side')
            ->assertSee('PHP Fundamentals & OOP Architecture')
            ->assertSee('REST API Design & MySQL Database')
            ->assertSee('Laravel Framework & Eloquent ORM')
            ->assertSee('Frontend Integration (Vue / React & Axios)')
            ->assertSee('Kembali ke Pilihan Modul');
    }

    public function test_query_parameter_redirects_to_dedicated_roadmap_page(): void
    {
        $this->actingAs($this->student)
            ->get('/materi?jalur=server')
            ->assertRedirect(route('materials.roadmap', ['track' => 'server']));

        $this->actingAs($this->student)
            ->get('/materi?jalur=client')
            ->assertRedirect(route('materials.roadmap', ['track' => 'client']));
    }

    public function test_material_show_displays_topic_detail_and_back_link_to_correct_track(): void
    {
        $serverTopic = Topic::where('slug', 'laravel-framework')->firstOrFail();

        $this->actingAs($this->student)
            ->get('/materi/'.$serverTopic->slug)
            ->assertOk()
            ->assertSee('Laravel Framework & Eloquent ORM')
            ->assertSee('Modul Server-Side')
            ->assertSee('Kembali ke Roadmap Modul Server-Side')
            ->assertSee(route('materials.roadmap', ['track' => 'server']));
    }

    public function test_topic_model_casts_track_to_module_track_enum(): void
    {
        $clientTopic = Topic::where('slug', 'html-semantics')->firstOrFail();
        $this->assertSame(ModuleTrack::Client, $clientTopic->track);

        $serverTopic = Topic::where('slug', 'laravel-framework')->firstOrFail();
        $this->assertSame(ModuleTrack::Server, $serverTopic->track);
    }
}
