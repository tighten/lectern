<?php

namespace Tighten\Lectern\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Tighten\Lectern\Http\Resources\ThreadResource;
use Tighten\Lectern\Services\SearchService;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService,
    ) {}

    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $results = $this->searchService->search($request->input('q'));

        return ThreadResource::collection($results);
    }
}
