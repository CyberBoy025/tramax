<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/store/page.tsx and
// store/[slug]/page.tsx — same public-scoped queries as Api\ProductController.
class ProductController extends Controller
{
    public function index(): View
    {
        return view('site.store.index', [
            'products' => Product::query()->where('status', 'Published')->orderBy('title')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::query()->where('slug', $slug)->where('status', 'Published')->firstOrFail();

        return view('site.store.show', ['product' => $product]);
    }
}
