<?php

namespace Tightenco\Lectern\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Tightenco\Lectern\Http\Requests\StorePostRequest;
use Tightenco\Lectern\Http\Requests\UpdatePostRequest;
use Tightenco\Lectern\Http\Requests\UploadPostImageRequest;
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

    public function uploadImage(UploadPostImageRequest $request, Post $post): JsonResponse
    {
        if (! config('lectern.images.enabled', true)) {
            return response()->json(['message' => 'Image uploads are disabled.'], 403);
        }

        $maxPerPost = config('lectern.images.max_per_post', 10);
        if ($post->getMedia('images')->count() >= $maxPerPost) {
            return response()->json(['message' => "Maximum of {$maxPerPost} images per post allowed."], 422);
        }

        $media = $post->addMediaFromRequest('image')
            ->toMediaCollection('images');

        $conversions = [];
        foreach (array_keys(config('lectern.images.conversions', [])) as $conversion) {
            $conversions[$conversion] = $media->getUrl($conversion);
        }

        return response()->json([
            'id' => $media->id,
            'url' => $media->getUrl(),
            'conversions' => $conversions,
        ]);
    }

    public function deleteImage(Post $post, int $mediaId): JsonResponse
    {
        $this->authorize('update', $post);

        $media = $post->getMedia('images')->firstWhere('id', $mediaId);

        if (! $media) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        $media->delete();

        return response()->json(null, 204);
    }
}
