<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderItem;
use App\Models\SupplierOrderStatus;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

function createSupplierForOrderTests(): Supplier
{
    return Supplier::query()->create([
        'name' => 'Supplier '.Str::upper(Str::random(8)),
        'tax_policy' => 0,
        'currency' => 'USD',
    ]);
}

function createProductForOrderTests(?string $name = null): Product
{
    return Product::query()->create([
        'sku' => 'SKU-'.Str::upper(Str::random(8)),
        'name' => $name ?? 'Product '.Str::upper(Str::random(6)),
    ]);
}

function createStatusForOrderTests(?string $name = null): SupplierOrderStatus
{
    return SupplierOrderStatus::query()->create([
        'name' => $name ?? 'Status '.Str::upper(Str::random(6)),
        'description' => 'Status description.',
    ]);
}

function orderPayload(
    Supplier $supplier,
    Product $product,
    ?SupplierOrderStatus $status = null,
    array $overrides = [],
): array {
    return array_replace_recursive([
        'order_number' => 'PO-'.Str::upper(Str::random(10)),
        'supplier_id' => $supplier->id,
        'status_id' => $status?->id,
        'tracking' => 'Tracking notes',
        'ordered_at' => '2026-03-01',
        'shipped_at' => '2026-03-02',
        'arrived_at' => '2026-03-03',
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_cost' => '10.50',
            ],
        ],
    ], $overrides);
}

test('guests are redirected from order routes', function () {
    $supplier = createSupplierForOrderTests();
    $product = createProductForOrderTests();
    $status = createStatusForOrderTests();
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-GUEST-001',
        'supplier_id' => $supplier->id,
        'status_id' => $status->id,
        'arrived_at' => now()->toDateString(),
    ]);

    $this->get(route('supplier-orders.index'))->assertRedirect(route('login'));
    $this->get(route('supplier-orders.create'))->assertRedirect(route('login'));
    $this->post(route('supplier-orders.store'), orderPayload($supplier, $product, $status))
        ->assertRedirect(route('login'));
    $this->get(route('supplier-orders.show', $order))->assertRedirect(route('login'));
    $this->get(route('supplier-orders.edit', $order))->assertRedirect(route('login'));
    $this->put(route('supplier-orders.update', $order), orderPayload($supplier, $product, $status))
        ->assertRedirect(route('login'));
    $this->delete(route('supplier-orders.destroy', $order))->assertRedirect(route('login'));
});

test('order requires valid supplier id', function () {
    $user = User::factory()->create();
    $product = createProductForOrderTests();

    $this->actingAs($user)
        ->from(route('supplier-orders.create'))
        ->post(route('supplier-orders.store'), orderPayload(createSupplierForOrderTests(), $product, null, [
            'supplier_id' => 999999,
        ]))
        ->assertRedirect(route('supplier-orders.create'))
        ->assertSessionHasErrors('supplier_id');
});

test('order rejects invalid status id', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $product = createProductForOrderTests();

    $this->actingAs($user)
        ->from(route('supplier-orders.create'))
        ->post(route('supplier-orders.store'), orderPayload($supplier, $product, null, [
            'status_id' => 999999,
        ]))
        ->assertRedirect(route('supplier-orders.create'))
        ->assertSessionHasErrors('status_id');
});

test('order items are validated', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $product = createProductForOrderTests();

    $cases = [
        'quantity' => [
            'payload' => ['items' => [['product_id' => $product->id, 'quantity' => 0, 'unit_cost' => '10.00']]],
            'error' => 'items.0.quantity',
        ],
        'unit_cost' => [
            'payload' => ['items' => [['product_id' => $product->id, 'quantity' => 1, 'unit_cost' => '-1.00']]],
            'error' => 'items.0.unit_cost',
        ],
        'product_id' => [
            'payload' => ['items' => [['product_id' => 999999, 'quantity' => 1, 'unit_cost' => '10.00']]],
            'error' => 'items.0.product_id',
        ],
    ];

    foreach ($cases as $case) {
        $this->actingAs($user)
            ->from(route('supplier-orders.create'))
            ->post(route('supplier-orders.store'), orderPayload($supplier, $product, null, $case['payload']))
            ->assertRedirect(route('supplier-orders.create'))
            ->assertSessionHasErrors($case['error']);
    }
});

test('order store persists order and items in transaction', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $status = createStatusForOrderTests('Placed');
    $firstProduct = createProductForOrderTests('First Product');
    $secondProduct = createProductForOrderTests('Second Product');

    $payload = orderPayload($supplier, $firstProduct, $status, [
        'order_number' => 'PO-STORE-001',
        'items' => [
            ['product_id' => $firstProduct->id, 'quantity' => 2, 'unit_cost' => '10.50'],
            ['product_id' => $secondProduct->id, 'quantity' => 1, 'unit_cost' => '25.00'],
        ],
    ]);

    $this->actingAs($user)
        ->post(route('supplier-orders.store'), $payload)
        ->assertRedirect(route('supplier-orders.index'));

    $order = SupplierOrder::query()->where('order_number', 'PO-STORE-001')->first();

    expect($order)->not->toBeNull();

    $this->assertDatabaseHas('supplier_orders', [
        'order_number' => 'PO-STORE-001',
        'supplier_id' => $supplier->id,
        'status_id' => $status->id,
    ]);

    $this->assertDatabaseHas('supplier_order_items', [
        'supplier_order_id' => $order->id,
        'product_id' => $firstProduct->id,
        'quantity' => 2,
        'unit_cost' => '10.50',
    ]);

    $this->assertDatabaseHas('supplier_order_items', [
        'supplier_order_id' => $order->id,
        'product_id' => $secondProduct->id,
        'quantity' => 1,
        'unit_cost' => '25.00',
    ]);
});

test('order update syncs items', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $status = createStatusForOrderTests('Pending');
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-UPDATE-001',
        'supplier_id' => $supplier->id,
        'status_id' => $status->id,
        'arrived_at' => '2026-03-03',
    ]);

    $oldProduct = createProductForOrderTests('Old Product');
    SupplierOrderItem::query()->create([
        'supplier_order_id' => $order->id,
        'product_id' => $oldProduct->id,
        'quantity' => 5,
        'unit_cost' => '15.00',
    ]);

    $newFirstProduct = createProductForOrderTests('New Product 1');
    $newSecondProduct = createProductForOrderTests('New Product 2');

    $payload = orderPayload($supplier, $newFirstProduct, $status, [
        'order_number' => 'PO-UPDATE-001',
        'items' => [
            ['product_id' => $newFirstProduct->id, 'quantity' => 1, 'unit_cost' => '9.99'],
            ['product_id' => $newSecondProduct->id, 'quantity' => 3, 'unit_cost' => '11.25'],
        ],
    ]);

    $this->actingAs($user)
        ->put(route('supplier-orders.update', $order), $payload)
        ->assertRedirect(route('supplier-orders.index'));

    expect($order->fresh()?->items()->count())->toBe(2);

    $this->assertDatabaseMissing('supplier_order_items', [
        'supplier_order_id' => $order->id,
        'product_id' => $oldProduct->id,
    ]);

    $actualItems = $order->fresh()->items()
        ->orderBy('product_id')
        ->get(['product_id', 'quantity', 'unit_cost'])
        ->map(fn (SupplierOrderItem $item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_cost' => number_format((float) $item->unit_cost, 2, '.', ''),
        ])
        ->values()
        ->all();

    expect($actualItems)->toBe([
        [
            'product_id' => $newFirstProduct->id,
            'quantity' => 1,
            'unit_cost' => '9.99',
        ],
        [
            'product_id' => $newSecondProduct->id,
            'quantity' => 3,
            'unit_cost' => '11.25',
        ],
    ]);
});

test('deleting order cascades to items', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $product = createProductForOrderTests();
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-DELETE-001',
        'supplier_id' => $supplier->id,
        'arrived_at' => '2026-03-03',
    ]);

    $item = SupplierOrderItem::query()->create([
        'supplier_order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_cost' => '8.00',
    ]);

    $this->actingAs($user)
        ->delete(route('supplier-orders.destroy', $order))
        ->assertRedirect(route('supplier-orders.index'));

    $this->assertDatabaseMissing('supplier_orders', ['id' => $order->id]);
    $this->assertDatabaseMissing('supplier_order_items', ['id' => $item->id]);
});

test('order index returns expected shape', function () {
    $this->withoutVite();

    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $status = createStatusForOrderTests('Received');
    $product = createProductForOrderTests('Indexed Product');
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-INDEX-001',
        'supplier_id' => $supplier->id,
        'status_id' => $status->id,
        'tracking' => 'TRACK-INDEX',
        'ordered_at' => '2026-03-01',
        'shipped_at' => '2026-03-02',
        'arrived_at' => '2026-03-03',
    ]);

    SupplierOrderItem::query()->create([
        'supplier_order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_cost' => '10.00',
    ]);

    $this->actingAs($user)
        ->get(route('supplier-orders.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('supplier-orders/Index')
            ->has('orders.data', 1, fn (Assert $orderData) => $orderData
                ->where('id', $order->id)
                ->where('supplier.name', $supplier->name)
                ->where('status.name', $status->name)
                ->where('items_count', 1)
                ->etc()
            )
        );
});

test('order show returns items with line totals', function () {
    $this->withoutVite();

    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $status = createStatusForOrderTests('Shipped');
    $firstProduct = createProductForOrderTests('Show Product 1');
    $secondProduct = createProductForOrderTests('Show Product 2');
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-SHOW-001',
        'supplier_id' => $supplier->id,
        'status_id' => $status->id,
        'arrived_at' => '2026-03-03',
    ]);

    SupplierOrderItem::query()->create([
        'supplier_order_id' => $order->id,
        'product_id' => $firstProduct->id,
        'quantity' => 2,
        'unit_cost' => '10.50',
    ]);

    SupplierOrderItem::query()->create([
        'supplier_order_id' => $order->id,
        'product_id' => $secondProduct->id,
        'quantity' => 1,
        'unit_cost' => '5.25',
    ]);

    $this->actingAs($user)
        ->get(route('supplier-orders.show', $order))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('supplier-orders/Show')
            ->where('order.id', $order->id)
            ->has('order.items', 2)
            ->where('order_total', 26.25)
        );
});

test('null status displays without error', function () {
    $this->withoutVite();

    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-NO-STATUS-001',
        'supplier_id' => $supplier->id,
        'status_id' => null,
        'arrived_at' => '2026-03-03',
    ]);

    $this->actingAs($user)
        ->get(route('supplier-orders.show', $order))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('supplier-orders/Show')
            ->where('order.id', $order->id)
            ->where('order.status', null)
        );
});

test('delete redirects to index with success', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-REDIRECT-001',
        'supplier_id' => $supplier->id,
        'arrived_at' => '2026-03-03',
    ]);

    $this->actingAs($user)
        ->delete(route('supplier-orders.destroy', $order))
        ->assertRedirect(route('supplier-orders.index'));
});

test('item constraints are enforced property holds across generated values', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $product = createProductForOrderTests();

    foreach (range(1, 100) as $iteration) {
        $quantity = random_int(-2, 3);
        $unitCost = number_format(random_int(-200, 500) / 100, 2, '.', '');

        $response = $this->actingAs($user)
            ->from(route('supplier-orders.create'))
            ->post(route('supplier-orders.store'), orderPayload($supplier, $product, null, [
                'order_number' => 'PO-CONSTRAINT-'.$iteration.'-'.Str::upper(Str::random(5)),
                'items' => [[
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                ]],
            ]));

        if ($quantity >= 1 && (float) $unitCost >= 0) {
            $response->assertRedirect(route('supplier-orders.index'));

            continue;
        }

        $response->assertRedirect(route('supplier-orders.create'));

        $expectedError = $quantity < 1 ? 'items.0.quantity' : 'items.0.unit_cost';
        $response->assertSessionHasErrors($expectedError);
    }
});

test('item references a valid product property holds across generated inputs', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForOrderTests();
    $validProduct = createProductForOrderTests();

    foreach (range(1, 50) as $iteration) {
        $this->actingAs($user)
            ->from(route('supplier-orders.create'))
            ->post(route('supplier-orders.store'), orderPayload($supplier, $validProduct, null, [
                'order_number' => 'PO-BAD-PRODUCT-'.$iteration.'-'.Str::upper(Str::random(5)),
                'items' => [[
                    'product_id' => 900000 + $iteration,
                    'quantity' => 1,
                    'unit_cost' => '10.00',
                ]],
            ]))
            ->assertRedirect(route('supplier-orders.create'))
            ->assertSessionHasErrors('items.0.product_id');
    }
});

test('order update item sync property holds across generated inputs', function () {
    $user = User::factory()->create();

    foreach (range(1, 20) as $iteration) {
        $supplier = createSupplierForOrderTests();
        $status = createStatusForOrderTests('Sync-'.$iteration);
        $order = SupplierOrder::query()->create([
            'order_number' => 'PO-SYNC-'.$iteration.'-'.Str::upper(Str::random(5)),
            'supplier_id' => $supplier->id,
            'status_id' => $status->id,
            'arrived_at' => '2026-03-03',
        ]);

        $initialCount = random_int(1, 3);
        foreach (range(1, $initialCount) as $initialItem) {
            $product = createProductForOrderTests();
            SupplierOrderItem::query()->create([
                'supplier_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => random_int(1, 5),
                'unit_cost' => number_format(random_int(100, 500) / 10, 2, '.', ''),
            ]);
        }

        $updatedCount = random_int(1, 4);
        $updatedItems = [];
        foreach (range(1, $updatedCount) as $updatedItem) {
            $product = createProductForOrderTests();
            $updatedItems[] = [
                'product_id' => $product->id,
                'quantity' => random_int(1, 5),
                'unit_cost' => number_format(random_int(100, 500) / 10, 2, '.', ''),
            ];
        }

        $this->actingAs($user)
            ->put(route('supplier-orders.update', $order), orderPayload($supplier, createProductForOrderTests(), $status, [
                'order_number' => $order->order_number,
                'items' => $updatedItems,
            ]))
            ->assertRedirect(route('supplier-orders.index'));

        $actualItems = $order->fresh()->items()
            ->orderBy('product_id')
            ->get(['product_id', 'quantity', 'unit_cost'])
            ->map(fn (SupplierOrderItem $item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_cost' => number_format((float) $item->unit_cost, 2, '.', ''),
            ])
            ->values()
            ->all();

        $expectedItems = collect($updatedItems)
            ->map(fn (array $item) => [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => number_format((float) $item['unit_cost'], 2, '.', ''),
            ])
            ->sortBy('product_id')
            ->values()
            ->all();

        expect($actualItems)->toBe($expectedItems);
    }
});

test('order deletion cascades to items property holds across generated inputs', function () {
    $user = User::factory()->create();

    foreach (range(1, 20) as $iteration) {
        $supplier = createSupplierForOrderTests();
        $order = SupplierOrder::query()->create([
            'order_number' => 'PO-CASCADE-'.$iteration.'-'.Str::upper(Str::random(5)),
            'supplier_id' => $supplier->id,
            'arrived_at' => '2026-03-03',
        ]);

        $itemIds = [];
        foreach (range(1, random_int(1, 4)) as $itemIteration) {
            $product = createProductForOrderTests();
            $item = SupplierOrderItem::query()->create([
                'supplier_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => random_int(1, 5),
                'unit_cost' => number_format(random_int(100, 500) / 10, 2, '.', ''),
            ]);

            $itemIds[] = $item->id;
        }

        $this->actingAs($user)
            ->delete(route('supplier-orders.destroy', $order))
            ->assertRedirect(route('supplier-orders.index'));

        expect(SupplierOrderItem::query()->whereIn('id', $itemIds)->count())->toBe(0);
    }
});

test('line total computation property holds across generated values', function () {
    foreach (range(1, 100) as $iteration) {
        $quantity = random_int(1, 10);
        $unitCost = random_int(0, 10000) / 100;
        $expectedTotal = round(array_sum(array_fill(0, $quantity, $unitCost)), 2);

        expect(round($quantity * $unitCost, 2))
            ->toBe($expectedTotal);
    }
});

test('order total computation property holds across generated item sets', function () {
    $user = User::factory()->create();

    foreach (range(1, 100) as $iteration) {
        $this->withoutVite();

        $supplier = createSupplierForOrderTests();
        $order = SupplierOrder::query()->create([
            'order_number' => 'PO-TOTAL-'.$iteration.'-'.Str::upper(Str::random(5)),
            'supplier_id' => $supplier->id,
            'arrived_at' => '2026-03-03',
        ]);

        $expectedTotal = 0.0;
        foreach (range(1, random_int(1, 4)) as $itemIteration) {
            $product = createProductForOrderTests();
            $quantity = random_int(1, 5);
            $unitCost = random_int(0, 10000) / 100;
            $expectedTotal += $quantity * $unitCost;

            SupplierOrderItem::query()->create([
                'supplier_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost' => number_format($unitCost, 2, '.', ''),
            ]);
        }

        $this->actingAs($user)
            ->get(route('supplier-orders.show', $order))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('supplier-orders/Show')
                ->where('order_total', fn (float $value): bool => abs($value - round($expectedTotal, 2)) < 0.0001)
            );
    }
});

test('authentication enforcement property holds across order and status routes', function () {
    $supplier = createSupplierForOrderTests();
    $product = createProductForOrderTests();
    $status = createStatusForOrderTests();
    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-AUTH-001',
        'supplier_id' => $supplier->id,
        'status_id' => $status->id,
        'arrived_at' => '2026-03-03',
    ]);

    $requests = [
        fn () => $this->get(route('supplier-order-statuses.index')),
        fn () => $this->post(route('supplier-order-statuses.store'), [
            'name' => 'Unauthenticated',
            'description' => 'Should redirect.',
        ]),
        fn () => $this->patch(route('supplier-order-statuses.update', $status), [
            'name' => 'Updated status',
            'description' => 'Updated description.',
        ]),
        fn () => $this->delete(route('supplier-order-statuses.destroy', $status)),
        fn () => $this->get(route('supplier-orders.index')),
        fn () => $this->get(route('supplier-orders.create')),
        fn () => $this->post(route('supplier-orders.store'), orderPayload($supplier, $product, $status)),
        fn () => $this->get(route('supplier-orders.show', $order)),
        fn () => $this->get(route('supplier-orders.edit', $order)),
        fn () => $this->put(route('supplier-orders.update', $order), orderPayload($supplier, $product, $status, [
            'order_number' => $order->order_number,
        ])),
        fn () => $this->delete(route('supplier-orders.destroy', $order)),
    ];

    foreach ($requests as $request) {
        $request()->assertRedirect(route('login'));
    }
});
