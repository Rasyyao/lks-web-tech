<?php

namespace Tests\Feature;

use App\Enums\ModuleTrack;
use App\Livewire\Student\MaterialIndex;
use App\Models\Topic;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            ->assertSee('Roadmap Pembelajaran')
            ->assertSee('Silabus LKS')
            ->assertSee('Modul Client-Side')
            ->assertSee('Modul Server-Side')
            ->assertSee('HTML5 Foundation & Web Semantics')
            ->assertSee('Modern CSS, Flexbox & Grid Layout');
    }

    public function test_default_track_is_client_and_displays_client_roadmap(): void
    {
        Livewire::actingAs($this->student)
            ->test(MaterialIndex::class)
            ->assertSet('activeTrack', 'client')
            ->assertSee('Modul Client-Side')
            ->assertSee('HTML5 Foundation & Web Semantics')
            ->assertSee('Modern CSS, Flexbox & Grid Layout')
            ->assertSee('JavaScript DOM Manipulation & Events');
    }

    public function test_switching_track_to_server_displays_server_roadmap(): void
    {
        Livewire::actingAs($this->student)
            ->test(MaterialIndex::class)
            ->call('setTrack', 'server')
            ->assertSet('activeTrack', 'server')
            ->assertSee('Modul Server-Side')
            ->assertSee('PHP Fundamentals & OOP Architecture')
            ->assertSee('REST API Design & MySQL Database')
            ->assertSee('Laravel Framework & Eloquent ORM')
            ->assertSee('Frontend Integration (Vue / React & Axios)');
    }

    public function test_query_parameter_sets_initial_active_track(): void
    {
        $this->actingAs($this->student)
            ->get('/materi?jalur=server')
            ->assertOk()
            ->assertSee('Laravel Framework & Eloquent ORM')
            ->assertSee('Frontend Integration (Vue / React & Axios)');
    }

    public function test_material_show_displays_topic_detail_and_back_link_to_correct_track(): void
    {
        $serverTopic = Topic::where('slug', 'laravel-framework')->firstOrFail();

        $this->actingAs($this->student)
            ->get('/materi/' . $serverTopic->slug)
            ->assertOk()
            ->assertSee('Laravel Framework & Eloquent ORM')
            ->assertSee('Modul Server-Side')
            ->assertSee('Kembali ke Roadmap Modul Server-Side')
            ->assertSee(route('materials.index', ['jalur' => 'server']));
    }

    public function test_topic_model_casts_track_to_module_track_enum(): void
    {
        $clientTopic = Topic::where('slug', 'html-semantics')->firstOrFail();
        $this->assertSame(ModuleTrack::Client, $clientTopic->track);

        $serverTopic = Topic::where('slug', 'laravel-framework')->firstOrFail();
        $this->assertSame(ModuleTrack::Server, $serverTopic->track);
    }
}
