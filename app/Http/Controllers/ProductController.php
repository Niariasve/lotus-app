<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\StoreRequest;
use App\Http\Requests\Products\UpdateRequest;
use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()
            ->select([
                'id',
                'sku',
                'name',
                'brand',
                'line',
                'height',
                'weight_est',
                'weight_real',
                'release_date',
            ])
            ->latest('id')
            ->get();

        return Inertia::render('products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('products/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        
        Product::create($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Product created successfully!'
        ]);

        return to_route('products.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('products/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->update($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Product updated successfully!'
        ]);

        return to_route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        Inertia::flash([
            'type' => 'success',
            'message' => 'Product deleted successfully!'
        ]);
        
        return to_route('products.index');
    }
}
