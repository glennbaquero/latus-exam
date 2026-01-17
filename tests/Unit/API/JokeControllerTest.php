<?php

namespace Tests\Unit\API;

use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Inertia\Testing\AssertableInertia as Assert;
use App\Services\JokeApiService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JokeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper to authenticate a verified user
     */
    protected function actingAsVerifiedUser(): void
    {
        $this->actingAs(
            User::factory()->create([
                'email_verified_at' => now(),
            ])
        );
    }

    #[Test]
    public function index_renders_dashboard_with_jokes(): void
    {
        $this->actingAsVerifiedUser();

        $mockService = Mockery::mock(JokeApiService::class);
        $mockService
            ->shouldReceive('get')
            ->once()
            ->with(3)
            ->andReturn(collect([
                ['id' => 1, 'setup' => 'Setup 1', 'punchline' => 'Punchline 1'],
                ['id' => 2, 'setup' => 'Setup 2', 'punchline' => 'Punchline 2'],
                ['id' => 3, 'setup' => 'Setup 3', 'punchline' => 'Punchline 3'],
            ]));

        $this->app->instance(JokeApiService::class, $mockService);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
    }

    #[Test]
    public function index_handles_service_exception(): void
    {
        $this->actingAsVerifiedUser();

        $mockService = Mockery::mock(JokeApiService::class);
        $mockService
            ->shouldReceive('get')
            ->once()
            ->andThrow(new \Exception('API error'));

        $this->app->instance(JokeApiService::class, $mockService);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) =>
                $page->component('Dashboard')
                    ->where('jokes', [])
                    ->where('error', 'Failed to load jokes. Please try again later.')
            );
    }

    #[Test]
    public function guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(302)
            ->assertRedirect(route('login'));
    }
}
