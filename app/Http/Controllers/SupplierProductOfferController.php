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
                'retail_price',
                'profit_percentage',
                'priority',
                'base_cost',
                'url',
                'is_available',
                'last_checked_at',
            ])
            ->with([
                'supplier:id,name,currency,tax_policy,estimated_shipping',
                'product:id,name,height,weight_est,weight_real',
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
        $validated['is_available'] = $validated['is_available'] ?? true;
        $validated['last_checked_at'] = now();

        SupplierProductOffer::create($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier product offer created successfully!',
        ]);

        return to_route('supplier-product-offer.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierProductOffer $supplierProductOffer)
    {
        $suppliers = Supplier::query()
            ->where('id', $supplierProductOffer->supplier->id)
            ->get();

        $products = Product::query()
            ->where('id', $supplierProductOffer->product->id)
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
        $newAvailability = (bool) $validated['is_available'];
        $oldAvailability = (bool) $supplierProductOffer->is_available;

        if ($newAvailability !== $oldAvailability) {
            $validated['last_checked_at'] = now();
        }

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

    /**
     * Toggle the supplier's product offer availability
     */
    public function toggleAvailability(SupplierProductOffer $supplierProductOffer) {
        $supplierProductOffer->update([
            'is_available' => !$supplierProductOffer->is_available,
            'last_checked_at' => now(),
        ]);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Availability updated for offer with id ' . $supplierProductOffer->id,
        ]);

        return back();
    }
}
