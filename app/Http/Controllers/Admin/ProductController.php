<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'media']);

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('brand') && $request->brand) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(20);
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.product.index', compact('products', 'brands', 'categories'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.product.create', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['id'] = Str::uuid();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        unset($validated['image']);
        $product = Product::create($validated);

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('product-images');
        }

        activity('product')
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->log('Created product: ' . $product->name);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'variants', 'media']);
        $activityLogs = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', Product::class)
            ->where('subject_id', $product->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.product.show', compact('product', 'activityLogs'));
    }

    public function edit(Product $product)
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $product->load('media');

        return view('admin.product.edit', compact('product', 'brands', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['image']);
        $product->update($validated);

        if ($request->hasFile('image')) {
            $product->clearMediaCollection('product-images');
            $product->addMediaFromRequest('image')->toMediaCollection('product-images');
        }

        activity('product')
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->log('Updated product: ' . $product->name);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->clearMediaCollection('product-images');
        $product->delete();

        activity('product')
            ->causedBy(auth()->user())
            ->withProperties(['deleted_product' => $productName])
            ->log('Deleted product: ' . $productName);

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function destroyMedia(Product $product, Media $media)
    {
        $media->delete();

        return back()->with('success', 'Image deleted successfully.');
    }
}
