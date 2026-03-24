<?php

use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderStatus;
use App\Models\User;
use Illuminate\Support\Str;

function createSupplierForStatusTests(): Supplier
{
    return Supplier::query()->create([
        'name' => 'Supplier '.Str::random(8),
        'tax_policy' => 0,
        'currency' => 'USD',
    ]);
}

test('guests are redirected from status routes', function () {
    $status = SupplierOrderStatus::query()->create([
        'name' => 'Pending',
        'description' => 'Waiting for assignment.',
    ]);

    $this->get(route('supplier-order-statuses.index'))
        ->assertRedirect(route('login'));

    $this->post(route('supplier-order-statuses.store'), [
        'name' => 'Placed',
        'description' => 'Sent to supplier.',
    ])->assertRedirect(route('login'));

    $this->patch(route('supplier-order-statuses.update', $status), [
        'name' => 'Updated Pending',
        'description' => 'Updated description.',
    ])->assertRedirect(route('login'));

    $this->delete(route('supplier-order-statuses.destroy', $status))
        ->assertRedirect(route('login'));
});

test('authenticated user can create a status', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('supplier-order-statuses.store'), [
            'name' => 'Placed',
            'description' => 'Sent to supplier.',
        ])
        ->assertRedirect(route('supplier-orders.index'));

    $this->assertDatabaseHas('supplier_order_statuses', [
        'name' => 'Placed',
        'description' => 'Sent to supplier.',
    ]);
});

test('duplicate status name is rejected', function () {
    $user = User::factory()->create();

    SupplierOrderStatus::query()->create([
        'name' => 'Shipped',
        'description' => 'Already exists.',
    ]);

    $this->actingAs($user)
        ->from(route('supplier-orders.index'))
        ->post(route('supplier-order-statuses.store'), [
            'name' => 'Shipped',
            'description' => 'Duplicate attempt.',
        ])
        ->assertRedirect(route('supplier-orders.index'))
        ->assertSessionHasErrors('name');
});

test('deleting a status nullifies order status id', function () {
    $user = User::factory()->create();
    $supplier = createSupplierForStatusTests();
    $status = SupplierOrderStatus::query()->create([
        'name' => 'Arrived',
        'description' => 'Order is complete.',
    ]);

    $order = SupplierOrder::query()->create([
        'order_number' => 'PO-DELETE-STATUS',
        'supplier_id' => $supplier->id,
        'status_id' => $status->id,
        'arrived_at' => now()->toDateString(),
    ]);

    $this->actingAs($user)
        ->delete(route('supplier-order-statuses.destroy', $status))
        ->assertRedirect();

    expect($order->fresh()?->status_id)->toBeNull();
});

test('status index returns all statuses', function () {
    $user = User::factory()->create();

    $firstStatus = SupplierOrderStatus::query()->create([
        'name' => 'Draft',
        'description' => 'Draft order.',
    ]);

    $secondStatus = SupplierOrderStatus::query()->create([
        'name' => 'Received',
        'description' => 'Received from supplier.',
    ]);

    $this->actingAs($user)
        ->get(route('supplier-order-statuses.index'))
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonFragment([
            'id' => $firstStatus->id,
            'name' => 'Draft',
        ])
        ->assertJsonFragment([
            'id' => $secondStatus->id,
            'name' => 'Received',
        ]);
});

test('status name uniqueness property holds across generated inputs', function () {
    $user = User::factory()->create();

    foreach (range(1, 50) as $iteration) {
        $name = 'Status '.Str::upper(Str::random(10)).$iteration;

        $this->actingAs($user)
            ->post(route('supplier-order-statuses.store'), [
                'name' => $name,
                'description' => 'Generated unique status.',
            ])
            ->assertRedirect(route('supplier-orders.index'));

        $this->actingAs($user)
            ->from(route('supplier-orders.index'))
            ->post(route('supplier-order-statuses.store'), [
                'name' => $name,
                'description' => 'Generated duplicate status.',
            ])
            ->assertRedirect(route('supplier-orders.index'))
            ->assertSessionHasErrors('name');
    }
});

test('status deletion nullifies order references property holds across generated orders', function () {
    $user = User::factory()->create();

    foreach (range(1, 20) as $iteration) {
        $supplier = createSupplierForStatusTests();
        $status = SupplierOrderStatus::query()->create([
            'name' => 'Status-'.$iteration.'-'.Str::upper(Str::random(6)),
            'description' => 'Generated status.',
        ]);

        $ordersToCreate = random_int(1, 4);

        foreach (range(1, $ordersToCreate) as $orderIteration) {
            SupplierOrder::query()->create([
                'order_number' => 'PO-'.$iteration.'-'.$orderIteration.'-'.Str::upper(Str::random(6)),
                'supplier_id' => $supplier->id,
                'status_id' => $status->id,
                'arrived_at' => now()->toDateString(),
            ]);
        }

        $this->actingAs($user)
            ->delete(route('supplier-order-statuses.destroy', $status))
            ->assertRedirect();

        expect(
            SupplierOrder::query()
                ->where('supplier_id', $supplier->id)
                ->whereNotNull('status_id')
                ->count()
        )->toBe(0);
    }
});
