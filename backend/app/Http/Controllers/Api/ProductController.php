<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->where('status', 'Published')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'price', 'image_url', 'category']);

        return response()->json(['data' => $products]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::query()->where('slug', $slug)->where('status', 'Published')->firstOrFail();

        return response()->json(['data' => $product]);
    }
}
