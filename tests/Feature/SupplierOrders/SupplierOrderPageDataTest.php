<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderItem;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('create page includes products for order items', function () {
    $this->withoutVite();

    $user = User::factory()->create();

    $product = Product::query()->create([
        'sku' => 'SKU-001',
        'name' => 'Product One',
    ]);

    $this->actingAs($user)
        ->get(route('supplier-orders.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('supplier-orders/Create')
            ->has('products', 1, fn (Assert $products) => $products
                ->where('id', $product->id)
                ->where('name', 'Product One')
                ->etc()
            )
            ->has('suppliers')
            ->has('statuses')
        );
});

test('edit page includes products for existing and new order items', function () {
    $this->withoutVite();

    $user = User::factory()->create();

    $supplier = Supplier::query()->create([
        'name' => 'Supplier One',
        'tax_policy' => 0,
        'currency' => 'USD',
    ]);

    $product = Product::query()->create([
        'sku' => 'SKU-EDIT-001',
        'name' => 'Editable Product',
    ]);

    $supplierOrder = SupplierOrder::query()->create([
        'order_number' => 'PO-001',
        'supplier_id' => $supplier->id,
        'arrived_at' => now()->toDateString(),
    ]);

    SupplierOrderItem::query()->create([
        'supplier_order_id' => $supplierOrder->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_cost' => 10,
    ]);

    $this->actingAs($user)
        ->get(route('supplier-orders.edit', $supplierOrder))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('supplier-orders/Edit')
            ->has('products', 1, fn (Assert $products) => $products
                ->where('id', $product->id)
                ->where('name', 'Editable Product')
                ->etc()
            )
            ->has('order', fn (Assert $order) => $order
                ->where('id', $supplierOrder->id)
                ->has('items', 1, fn (Assert $items) => $items
                    ->where('product_id', $product->id)
                    ->has('product', fn (Assert $itemProduct) => $itemProduct
                        ->where('id', $product->id)
                        ->where('name', 'Editable Product')
                        ->etc()
                    )
                    ->etc()
                )
                ->etc()
            )
        );
});
