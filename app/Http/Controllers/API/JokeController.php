<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
