<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierOrders\StoreRequest;
use App\Http\Requests\SupplierOrders\UpdateRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderItem;
use App\Models\SupplierOrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SupplierOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $orders = SupplierOrder::query()
            ->with([
                'supplier:id,name',
                'status:id,name',
            ])
            ->withCount('items')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $statuses = SupplierOrderStatus::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('supplier-orders/Index', [
            'orders' => $orders,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $products = Product::query()
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $statuses = SupplierOrderStatus::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('supplier-orders/Create', [
            'products' => $products,
            'suppliers' => $suppliers,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        unset($validated['items']);

        DB::transaction(function () use ($validated, $items): void {
            $supplierOrder = SupplierOrder::create($validated);

            $supplierOrder->items()->createMany($items);
        });

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier order created successfully!',
        ]);

        return to_route('supplier-orders.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierOrder $supplierOrder): Response
    {
        $supplierOrder->load([
            'supplier',
            'status',
            'items.product',
        ]);

        $orderTotal = $supplierOrder->items->sum(
            fn (SupplierOrderItem $item): float => $item->quantity * (float) $item->unit_cost,
        );

        return Inertia::render('supplier-orders/Show', [
            'order' => $supplierOrder,
            'order_total' => $orderTotal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierOrder $supplierOrder): Response
    {
        $supplierOrder->load('items.product');

        $products = Product::query()
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $statuses = SupplierOrderStatus::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('supplier-orders/Edit', [
            'order' => $supplierOrder,
            'products' => $products,
            'suppliers' => $suppliers,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, SupplierOrder $supplierOrder): RedirectResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        unset($validated['items']);

        DB::transaction(function () use ($supplierOrder, $validated, $items): void {
            $supplierOrder->update($validated);
            $supplierOrder->items()->delete();
            $supplierOrder->items()->createMany($items);
        });

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier order updated successfully!',
        ]);

        return to_route('supplier-orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierOrder $supplierOrder): RedirectResponse
    {
        $supplierOrder->delete();

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier order deleted successfully!',
        ]);

        return to_route('supplier-orders.index');
    }
}
