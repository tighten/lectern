<?php

namespace Tightenco\Lectern\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Tightenco\Lectern\Http\Resources\CategoryResource;
use Tightenco\Lectern\Models\Category;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with('children')
            ->withCount('threads')
            ->orderBy('order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category): CategoryResource
    {
        $category->load('children')->loadCount('threads');

        return new CategoryResource($category);
    }
}
