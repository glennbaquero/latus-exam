<?php

namespace Tests\Unit\Services;


use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use App\Services\JokeApiService;

class JokeApiServiceTest extends TestCase
{
    private JokeApiService $service;
    private string $apiUrl;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->apiUrl = 'https://official-joke-api.appspot.com/jokes/programming/ten';
        Config::set('joke.url', $this->apiUrl);
        
        $this->service = new JokeApiService();
    }

    #[Test]
    public function it_can_fetch_jokes_successfully(): void
    {
        $mockResponse = [
            ['id' => 1, 'type' => 'programming', 'setup' => 'Setup 1', 'punchline' => 'Punchline 1'],
            ['id' => 2, 'type' => 'programming', 'setup' => 'Setup 2', 'punchline' => 'Punchline 2'],
            ['id' => 4, 'type' => 'programming', 'setup' => 'Setup 4', 'punchline' => 'Punchline 4'],
        ];

        Http::fake([
            $this->apiUrl => Http::response($mockResponse, 200)
        ]);

        $result = $this->service->get(3);

        $this->assertCount(3, $result);
        $this->assertEquals(1, $result->first()['id']);
        $this->assertEquals('Setup 1', $result->first()['setup']);
    }

    #[Test]
    public function it_returns_default_limit_of_three_jokes(): void
    {
        $mockResponse = [
            ['id' => 1, 'type' => 'programming', 'setup' => 'Setup 1', 'punchline' => 'Punchline 1'],
            ['id' => 2, 'type' => 'programming', 'setup' => 'Setup 2', 'punchline' => 'Punchline 2'],
            ['id' => 3, 'type' => 'programming', 'setup' => 'Setup 3', 'punchline' => 'Punchline 3'],
            ['id' => 4, 'type' => 'programming', 'setup' => 'Setup 4', 'punchline' => 'Punchline 4'],
            ['id' => 5, 'type' => 'programming', 'setup' => 'Setup 5', 'punchline' => 'Punchline 5'],
            ['id' => 6, 'type' => 'programming', 'setup' => 'Setup 6', 'punchline' => 'Punchline 6'],
            ['id' => 7, 'type' => 'programming', 'setup' => 'Setup 7', 'punchline' => 'Punchline 7'],
            ['id' => 8, 'type' => 'programming', 'setup' => 'Setup 7', 'punchline' => 'Punchline 8'],
            ['id' => 9, 'type' => 'programming', 'setup' => 'Setup 9', 'punchline' => 'Punchline 8'],
            ['id' => 10, 'type' => 'programming', 'setup' => 'Setup 10', 'punchline' => 'Punchline 10'],
        ];

        Http::fake([
            $this->apiUrl => Http::response($mockResponse, 200)
        ]);

        $result = $this->service->get();

        $this->assertCount(3, $result);
    }

    #[Test]
    public function it_can_fetch_custom_limit_of_jokes(): void
    {
        $mockResponse = [
            ['id' => 1, 'type' => 'programming', 'setup' => 'Setup 1', 'punchline' => 'Punchline 1'],
            ['id' => 2, 'type' => 'programming', 'setup' => 'Setup 2', 'punchline' => 'Punchline 2'],
            ['id' => 3, 'type' => 'programming', 'setup' => 'Setup 3', 'punchline' => 'Punchline 3'],
            ['id' => 4, 'type' => 'programming', 'setup' => 'Setup 4', 'punchline' => 'Punchline 4'],
            ['id' => 5, 'type' => 'programming', 'setup' => 'Setup 5', 'punchline' => 'Punchline 5'],
        ];

        Http::fake([
            $this->apiUrl => Http::response($mockResponse, 200)
        ]);

        $result = $this->service->get(5);

        $this->assertCount(5, $result);
    }

    #[Test]
    public function it_handles_less_results_than_limit(): void
    {
        $mockResponse = [
            ['id' => 1, 'type' => 'programming', 'setup' => 'Setup 1', 'punchline' => 'Punchline 1'],
            ['id' => 5, 'type' => 'programming', 'setup' => 'Setup 5', 'punchline' => 'Punchline 5'],
        ];

        Http::fake([
            $this->apiUrl => Http::response($mockResponse, 200)
        ]);

        $result = $this->service->get(5);

        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_retries_on_failure_and_eventually_succeeds(): void
    {
        Http::fake([
            $this->apiUrl => Http::sequence()
                ->push('Server Error', 500)
                ->push('Server Error', 500)
                ->push([
                    ['id' => 1, 'type' => 'programming', 'setup' => 'Setup 1', 'punchline' => 'Punchline 1'],
                    ['id' => 2, 'type' => 'programming', 'setup' => 'Setup 2', 'punchline' => 'Punchline 2'],
                    ['id' => 3, 'type' => 'programming', 'setup' => 'Setup 3', 'punchline' => 'Punchline 3'],
                ], 200)
        ]);

        $result = $this->service->get(3);

        $this->assertCount(3, $result);
        Http::assertSentCount(3);
    }

    #[Test]
    public function it_throws_exception_after_max_retries(): void
    {
        Http::fake([
            $this->apiUrl => Http::response('Server Error', 500)
        ]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'API request failed') 
                    && $context['url'] === $this->apiUrl;
            });

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch data after multiple attempts');

        $this->service->get(3);
    }

    #[Test]
    public function it_logs_error_on_failure(): void
    {
        Http::fake([
            $this->apiUrl => Http::response('Server Error', 500)
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with(
                \Mockery::on(fn($msg) => str_contains($msg, 'API request failed')),
                \Mockery::on(fn($context) => $context['url'] === $this->apiUrl)
            );

        try {
            $this->service->get(3);
        } catch (\Exception $e) {
            $this->assertEquals('Failed to fetch data after multiple attempts', $e->getMessage());
        }
    }

    #[Test]
    public function it_handles_connection_exception(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Connection timeout');
        });

        Log::shouldReceive('error')->once();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch data after multiple attempts');

        $this->service->get(3);
    }

    #[Test]
    public function it_returns_collection_instance(): void
    {
        Http::fake([
            $this->apiUrl => Http::response([
                ['id' => 1, 'type' => 'programming', 'setup' => 'Setup 1', 'punchline' => 'Punchline 1']
            ], 200)
        ]);

        $result = $this->service->get(1);

        $this->assertInstanceOf(Collection::class, $result);
    }

    #[Test]
    public function it_handles_empty_response(): void
    {
        Http::fake([
            $this->apiUrl => Http::response([], 200)
        ]);

        $result = $this->service->get(3);

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    #[Test]
    public function it_retries_exactly_three_times_before_giving_up(): void
    {
        Http::fake([
            $this->apiUrl => Http::response('Error', 500)
        ]);

        Log::shouldReceive('error')->once();

        try {
            $this->service->get(1);
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            Http::assertSentCount(3);
        }
    }

    #[Test]
    public function it_handles_404_not_found_error(): void
    {
        Http::fake([
            $this->apiUrl => Http::response('Cannot GET /jokes/programming/ten/x', 404)
        ]);

        Log::shouldReceive('error')->once();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch data after multiple attempts');

        $this->service->get(3);
    }
}
