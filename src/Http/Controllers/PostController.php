<?php

namespace Tightenco\Lectern\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Tightenco\Lectern\Http\Requests\StorePostRequest;
use Tightenco\Lectern\Http\Requests\UpdatePostRequest;
use Tightenco\Lectern\Http\Resources\PostResource;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Services\MentionParser;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected MentionParser $mentionParser,
    ) {}

    public function index(Thread $thread): AnonymousResourceCollection
    {
        $mode = config('lectern.threading.mode');

        $query = $thread->posts()->with('user')->withCount(['reactions', 'replies']);

        if ($mode === 'flat') {
            $posts = $query->oldest()->paginate(config('lectern.pagination.posts'));
        } else {
            $posts = $query->whereNull('parent_id')
                ->with(['replies' => function ($query) {
                    $query->with('user')->withCount('reactions')->oldest();
                }])
                ->oldest()
                ->paginate(config('lectern.pagination.posts'));
        }

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request, Thread $thread): PostResource
    {
        $this->authorize('create', [Post::class, $thread]);

        $post = $thread->posts()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $request->input('parent_id'),
            'body' => $request->input('body'),
        ]);

        $this->mentionParser->parse($post);

        $post->load('user');

        return new PostResource($post);
    }

    public function show(Post $post): PostResource
    {
        $post->load('user')->loadCount(['reactions', 'replies']);

        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        $this->mentionParser->syncMentions($post);

        return new PostResource($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }

    public function replies(Post $post): AnonymousResourceCollection
    {
        $replies = $post->replies()
            ->with('user')
            ->withCount('reactions')
            ->oldest()
            ->paginate(config('lectern.pagination.posts'));

        return PostResource::collection($replies);
    }
}
