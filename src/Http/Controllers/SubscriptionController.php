<?php

namespace Tightenco\Lectern\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Tightenco\Lectern\Http\Resources\SubscriptionResource;
use Tightenco\Lectern\Models\Category;
use Tightenco\Lectern\Models\Subscription;
use Tightenco\Lectern\Models\Thread;

class SubscriptionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $subscriptions = Subscription::query()
            ->where('user_id', $request->user()->id)
            ->with('subscribable')
            ->latest('created_at')
            ->paginate(config('lectern.pagination.subscriptions', 20));

        return SubscriptionResource::collection($subscriptions);
    }

    public function subscribeToThread(Request $request, Thread $thread): SubscriptionResource
    {
        $subscription = Subscription::firstOrCreate([
            'user_id' => $request->user()->id,
            'subscribable_type' => 'lectern_thread',
            'subscribable_id' => $thread->id,
        ]);

        return new SubscriptionResource($subscription);
    }

    public function unsubscribeFromThread(Request $request, Thread $thread): JsonResponse
    {
        Subscription::query()
            ->where('user_id', $request->user()->id)
            ->where('subscribable_type', 'lectern_thread')
            ->where('subscribable_id', $thread->id)
            ->delete();

        return response()->json(null, 204);
    }

    public function subscribeToCategory(Request $request, Category $category): SubscriptionResource
    {
        $subscription = Subscription::firstOrCreate([
            'user_id' => $request->user()->id,
            'subscribable_type' => 'lectern_category',
            'subscribable_id' => $category->id,
        ]);

        return new SubscriptionResource($subscription);
    }

    public function unsubscribeFromCategory(Request $request, Category $category): JsonResponse
    {
        Subscription::query()
            ->where('user_id', $request->user()->id)
            ->where('subscribable_type', 'lectern_category')
            ->where('subscribable_id', $category->id)
            ->delete();

        return response()->json(null, 204);
    }
}
