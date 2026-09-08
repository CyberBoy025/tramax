<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Read gated to Super Administrator, Management, Content Manager; write
// gated to Super Administrator + Content Manager; delete further
// restricted to Super Administrator — per discovery.md §3's Store row.
// Unlike the public GET /products, this returns Draft items too.
class ProductAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Product::query()->orderBy('title')->get()]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $product = Product::create($data);

        return response()->json(['data' => $product], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $this->validated($request, sometimes: true);
        $product->update($data);

        return response()->json(['data' => $product->fresh()]);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            return response()->json(['message' => 'Only a Super Administrator can delete a product.'], 403);
        }

        $product->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $rule = fn (string $r) => $sometimes ? ['sometimes', $r] : ['required', $r];

        return $request->validate([
            'title' => $rule('string'),
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:Draft,Published'],
        ]);
    }
}
