<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ProfilesEstablishment;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    /** @test */
    public function establishment_can_create_job()
    {
        $user = User::factory()->create(['role' => 'establishment']);
        $establishment = ProfilesEstablishment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'Garçom para Evento',
                'description' => 'Procuramos garçom experiente',
                'role' => 'Garçom',
                'rate' => 80.00,
                'rate_type' => 'Fixed',
                'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
                'end_time' => now()->addDays(1)->addHours(4)->format('Y-m-d H:i:s'),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'job' => ['id', 'title', 'rate', 'status']
            ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Garçom para Evento',
            'establishment_id' => $establishment->id,
            'status' => 'Open',
        ]);
    }

    /** @test */
    public function professional_cannot_create_job()
    {
        $user = User::factory()->create(['role' => 'professional']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'Test Job',
                'rate' => 80,
                'rate_type' => 'Fixed',
                'start_time' => now()->addDays(1),
                'end_time' => now()->addDays(1)->addHours(4),
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function can_list_jobs_with_filters()
    {
        $establishment = ProfilesEstablishment::factory()->create();
        
        Job::factory()->create([
            'establishment_id' => $establishment->id,
            'role' => 'Garçom',
            'rate' => 80,
            'status' => 'Open',
        ]);

        Job::factory()->create([
            'establishment_id' => $establishment->id,
            'role' => 'Bartender',
            'rate' => 100,
            'status' => 'Open',
        ]);

        $response = $this->getJson('/api/jobs?role=Garçom');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function only_owner_can_update_job()
    {
        $owner = User::factory()->create(['role' => 'establishment']);
        $establishment = ProfilesEstablishment::factory()->create(['user_id' => $owner->id]);
        $job = Job::factory()->create(['establishment_id' => $establishment->id]);

        $otherUser = User::factory()->create(['role' => 'establishment']);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->putJson("/api/jobs/{$job->id}", [
                'rate' => 90,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_cancel_filled_job()
    {
        $user = User::factory()->create(['role' => 'establishment']);
        $establishment = ProfilesEstablishment::factory()->create(['user_id' => $user->id]);
        $job = Job::factory()->create([
            'establishment_id' => $establishment->id,
            'status' => 'Filled',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/jobs/{$job->id}");

        $response->assertStatus(400);
    }
}
