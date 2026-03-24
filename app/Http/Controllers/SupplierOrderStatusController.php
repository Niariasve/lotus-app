<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierOrderStatuses\StoreRequest;
use App\Http\Requests\SupplierOrderStatuses\UpdateRequest;
use App\Models\SupplierOrderStatus;
use Inertia\Inertia;

class SupplierOrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SupplierOrderStatus::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        SupplierOrderStatus::create($request->validated());

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier order status created successfully!',
        ]);

        return to_route('supplier-orders.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, SupplierOrderStatus $supplierOrderStatus)
    {
        $supplierOrderStatus->update($request->validated());

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier order status updated successfully!',
        ]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierOrderStatus $supplierOrderStatus)
    {
        $supplierOrderStatus->delete();

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier order status deleted successfully!',
        ]);

        return back();
    }
}
