<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierProductOffers\StoreRequest;
use App\Http\Requests\SupplierProductOffers\UpdateRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProductOffer;
use Inertia\Inertia;

class SupplierProductOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplierProductOffers = SupplierProductOffer::query()
            ->select([
                'id',
                'supplier_id',
                'product_id',
                'base_cost',
                'currency',
                'estimated_tax',
                'estimated_shipping',
                'other_fees',
                'is_available',
                'last_checked_at',
            ])
            ->with([
                'supplier:id,name',
                'product:id,name,sku',
            ])
            ->latest('id')
            ->get();

        return Inertia::render('supplierProductOffer/Index', [
            'supplierProductOffers' => $supplierProductOffers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('supplierProductOffer/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        SupplierProductOffer::create($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier product offer created successfully!',
        ]);

        return to_route('supplier-product-offer.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierProductOffer $supplierProductOffer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierProductOffer $supplierProductOffer)
    {
        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('supplierProductOffer/Edit', [
            'supplierProductOffer' => $supplierProductOffer,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, SupplierProductOffer $supplierProductOffer)
    {
        $validated = $request->validated();

        $supplierProductOffer->update($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier product offer updated successfully!',
        ]);

        return to_route('supplier-product-offer.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierProductOffer $supplierProductOffer)
    {
        $supplierProductOffer->delete();

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier product offer deleted successfully!',
        ]);

        return back();
    }
}
