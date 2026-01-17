<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Inertia\Inertia;

use App\Http\Controllers\Controller;

use App\Services\JokeApiService;

use App\Http\Resources\API\JokeResource;

class JokeController extends Controller
{
    public function __construct(protected JokeApiService $jokeService){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $result = $this->jokeService->get(3);
            return Inertia::render('Dashboard', [
                'jokes' => JokeResource::collection($result),
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Dashboard', [
                'jokes' => [],
                'error' => 'Failed to load jokes. Please try again later.'
            ]);
        }
    }
}
