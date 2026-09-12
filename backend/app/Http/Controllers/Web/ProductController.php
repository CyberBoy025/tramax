<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\ProductAdminController.
class ProductController extends Controller
{
    private const STATUSES = ['Draft', 'Published'];

    public function index(): View
    {
        return view('admin.store.index', ['products' => Product::query()->orderBy('title')->get()]);
    }

    public function create(): View
    {
        return view('admin.store.form', ['product' => null, 'statuses' => self::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        Product::create($data);

        return redirect()->route('admin.store.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.store.form', ['product' => $product, 'statuses' => self::STATUSES]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, sometimes: true));

        return redirect()->route('admin.store.index')->with('success', 'Product updated.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        if (! $request->user()->hasRole(Role::SUPER_ADMIN)) {
            abort(403, 'Only a Super Administrator can delete a product.');
        }

        $product->delete();

        return redirect()->route('admin.store.index')->with('success', 'Product deleted.');
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
            'status' => ['nullable', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);
    }
}
