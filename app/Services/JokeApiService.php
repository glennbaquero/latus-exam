<?php

namespace App\Services;

use Illuminate\Support\Collection;

use Http;

class JokeApiService
{
    private $url;
    
    public function __construct()
    {
        $this->url = config('joke.url');
    }

    public function get(int $limit = 3): Collection
    {
        try {
            $response =  Http::retry(3, 1000)
                ->timeout(30)
                ->get($this->url)
                ->throw()
                ->json();

            return collect($response)->take($limit);
        } catch (\Exception $e) {
            \Log::error("API request failed: " . $e->getMessage(), [
                'url' => $this->url
            ]);
            throw new \Exception("Failed to fetch data after multiple attempts");
        }
    }
}
