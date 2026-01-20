<?php

namespace Tightenco\Lectern\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Tightenco\Lectern\Http\Requests\StoreThreadRequest;
use Tightenco\Lectern\Http\Requests\UpdateThreadRequest;
use Tightenco\Lectern\Http\Resources\ThreadResource;
use Tightenco\Lectern\Models\Category;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Services\MentionParser;

class ThreadController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected MentionParser $mentionParser,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $threads = Thread::query()
            ->with(['user', 'category', 'latestPost'])
            ->withCount('posts')
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(config('lectern.pagination.threads'));

        return ThreadResource::collection($threads);
    }

    public function indexByCategory(Category $category): AnonymousResourceCollection
    {
        $threads = $category->threads()
            ->with(['user', 'latestPost'])
            ->withCount('posts')
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(config('lectern.pagination.threads'));

        return ThreadResource::collection($threads);
    }

    public function store(StoreThreadRequest $request, Category $category): ThreadResource
    {
        $this->authorize('create', [Thread::class, $category]);

        $thread = $category->threads()->create([
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')) . '-' . Str::random(8),
        ]);

        $post = $thread->posts()->create([
            'user_id' => $request->user()->id,
            'body' => $request->input('body'),
        ]);

        $this->mentionParser->parse($post);

        $thread->load(['user', 'firstPost']);

        return new ThreadResource($thread);
    }

    public function show(Thread $thread): ThreadResource
    {
        $thread->load(['user', 'category', 'firstPost.user'])->loadCount('posts');

        return new ThreadResource($thread);
    }

    public function update(UpdateThreadRequest $request, Thread $thread): ThreadResource
    {
        $this->authorize('update', $thread);

        $thread->update($request->validated());

        return new ThreadResource($thread);
    }

    public function destroy(Thread $thread): JsonResponse
    {
        $this->authorize('delete', $thread);

        $thread->delete();

        return response()->json(null, 204);
    }

    public function lock(Thread $thread): ThreadResource
    {
        $this->authorize('lock', $thread);

        $thread->lock();

        return new ThreadResource($thread);
    }

    public function unlock(Thread $thread): ThreadResource
    {
        $this->authorize('unlock', $thread);

        $thread->unlock();

        return new ThreadResource($thread);
    }

    public function pin(Thread $thread): ThreadResource
    {
        $this->authorize('pin', $thread);

        $thread->pin();

        return new ThreadResource($thread);
    }

    public function unpin(Thread $thread): ThreadResource
    {
        $this->authorize('unpin', $thread);

        $thread->unpin();

        return new ThreadResource($thread);
    }
}
