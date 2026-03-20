# Design Document: Supplier Orders

## Overview

The Supplier Orders feature adds a full purchase-order lifecycle to the application. After a user identifies the best sourcing option via `SupplierProductOffer`, they create a `SupplierOrder` against the chosen supplier. The order tracks line items (confirmed unit costs and quantities), shipping lifecycle dates, append-only tracking numbers, user-managed statuses, and one-or-more payment installments. Orders can be split: a subset of items moves to a new order while the original retains its number and remaining items.

The feature is implemented as a standard Laravel/Inertia resource following the conventions already established in the codebase (RESTful controllers, Form Requests, Inertia pages, TanStack Vue Table, Reka UI primitives).

---

## Architecture

The feature follows the existing layered architecture:

```
Browser (Vue 3 + Inertia)
    │
    ▼
Laravel Controllers (HTTP layer)
    │  Form Requests (validation)
    ▼
Eloquent Models (business logic + relationships)
    │
    ▼
MySQL database (5 new tables)
```

### Request Flow

- **Read** (index, edit): Controller eager-loads relationships → `Inertia::render()` with typed props
- **Write** (store, update, destroy): Form Request validates → Model mutates → `Inertia::flash()` → `to_route()`
- **Split**: Dedicated `POST /supplier-orders/{order}/split` → `SupplierOrderController@split` → DB transaction creates new order + moves items → redirect to new order edit page
- **Status management**: `SupplierOrderStatusController` exposes JSON-style endpoints consumed by `StatusDrawer.vue` via Inertia visits / `router.post/patch/delete`

---

## Components and Interfaces

### Backend Controllers

#### `SupplierOrderController`
`app/Http/Controllers/SupplierOrderController.php`

| Method | Route | Description |
|--------|-------|-------------|
| `index` | `GET /supplier-orders` | Paginated/full list with eager-loaded supplier, status, item count |
| `create` | `GET /supplier-orders/create` | Form with suppliers, statuses, products |
| `store` | `POST /supplier-orders` | Validate + persist order + items |
| `edit` | `GET /supplier-orders/{order}/edit` | Form pre-filled with order, items, trackings, payments |
| `update` | `PUT /supplier-orders/{order}` | Validate + update order + sync items |
| `destroy` | `DELETE /supplier-orders/{order}` | Cascade delete via model |
| `split` | `POST /supplier-orders/{order}/split` | Split order into two; DB transaction |
| `updateStatus` | `PATCH /supplier-orders/{order}/status` | Inline status update from index table |

#### `SupplierOrderStatusController`
`app/Http/Controllers/SupplierOrderStatusController.php`

Non-resource, API-style — consumed by `StatusDrawer.vue`.

| Method | Route | Description |
|--------|-------|-------------|
| `index` | `GET /supplier-order-statuses` | Return all statuses ordered by `sort_order` |
| `store` | `POST /supplier-order-statuses` | Create new status |
| `update` | `PUT /supplier-order-statuses/{status}` | Update name/description |
| `destroy` | `DELETE /supplier-order-statuses/{status}` | Delete if not in use |
| `reorder` | `PATCH /supplier-order-statuses/reorder` | Bulk update `sort_order` |

#### `SupplierOrderTrackingController`
`app/Http/Controllers/SupplierOrderTrackingController.php`

| Method | Route | Description |
|--------|-------|-------------|
| `store` | `POST /supplier-orders/{order}/trackings` | Append a tracking number |

#### `SupplierOrderPaymentController`
`app/Http/Controllers/SupplierOrderPaymentController.php`

| Method | Route | Description |
|--------|-------|-------------|
| `store` | `POST /supplier-orders/{order}/payments` | Add a payment |
| `destroy` | `DELETE /supplier-orders/{order}/payments/{payment}` | Delete a single payment |

### Form Requests

```
app/Http/Requests/SupplierOrders/
├── StoreRequest.php
├── UpdateRequest.php
├── UpdateStatusRequest.php
└── SplitRequest.php

app/Http/Requests/SupplierOrderStatuses/
├── StoreRequest.php
├── UpdateRequest.php
└── ReorderRequest.php

app/Http/Requests/SupplierOrderTrackings/
└── StoreRequest.php

app/Http/Requests/SupplierOrderPayments/
└── StoreRequest.php
```

### Frontend Pages

```
resources/js/pages/supplierOrders/
├── Index.vue    — TanStack table; split-order indicator; inline status select per row
├── Create.vue   — order form with dynamic item rows
└── Edit.vue     — order form + tracking list + payment list
```

The `Index.vue` status column renders a `Select` component populated from the `statuses` prop (all `SupplierOrderStatus` records passed by the controller). On change it fires `router.patch(route('supplier-orders.status', order.id), { status_id })`, which calls `updateStatus` and redirects back to the index, reloading the page data.

### Shared Component

`resources/js/components/StatusDrawer.vue`

A reusable `Sheet`-based drawer that accepts props for entity label and endpoint URLs. Supports create, edit, delete, and drag-to-reorder. Designed to be used for any status-driven entity (supplier orders today, others in the future).

Props interface:
```ts
interface StatusDrawerProps {
    entityLabel: string          // e.g. "Supplier Order Status"
    endpoints: {
        index: string
        store: string
        update: (id: number) => string
        destroy: (id: number) => string
        reorder: string
    }
}
```

### TypeScript Types

`resources/js/features/supplier-orders/types/supplierOrders.ts` — re-exported from `resources/js/types/index.ts`.

---

## Data Models

### Database Schema

#### `supplier_order_statuses`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
name            VARCHAR(255) NOT NULL UNIQUE
description     TEXT NULL
sort_order      INT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### `supplier_orders`
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
supplier_id     BIGINT UNSIGNED NOT NULL  -- FK → suppliers.id
status_id       BIGINT UNSIGNED NOT NULL  -- FK → supplier_order_statuses.id
order_number    VARCHAR(255) NOT NULL
ordered_at      DATE NULL
shipped_at      DATE NULL
arrived_at      DATE NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### `supplier_order_items`
```sql
id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
supplier_order_id   BIGINT UNSIGNED NOT NULL  -- FK → supplier_orders.id CASCADE DELETE
product_id          BIGINT UNSIGNED NOT NULL  -- FK → products.id
unit_cost_final     DECIMAL(8,2) NOT NULL
quantity            INT NOT NULL
notes               TEXT NULL
created_at          TIMESTAMP   -- no updated_at
```

#### `supplier_order_trackings`
```sql
id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
supplier_order_id   BIGINT UNSIGNED NOT NULL  -- FK → supplier_orders.id CASCADE DELETE
tracking            VARCHAR(255) NOT NULL
created_at          TIMESTAMP   -- no updated_at
```

#### `supplier_order_payments`
```sql
id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
supplier_order_id   BIGINT UNSIGNED NOT NULL  -- FK → supplier_orders.id CASCADE DELETE
amount              DECIMAL(8,2) NOT NULL
paid_at             DATE NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Eloquent Models

#### `SupplierOrder`
```php
protected $fillable = [
    'supplier_id', 'status_id', 'order_number',
    'ordered_at', 'shipped_at', 'arrived_at',
];

protected function casts(): array {
    return [
        'ordered_at' => 'date:Y-m-d',
        'shipped_at' => 'date:Y-m-d',
        'arrived_at' => 'date:Y-m-d',
    ];
}

// Relationships: belongsTo Supplier, belongsTo SupplierOrderStatus
// hasMany SupplierOrderItem (cascade), hasMany SupplierOrderTracking (cascade), hasMany SupplierOrderPayment (cascade)
```

#### `SupplierOrderItem`
```php
protected $fillable = [
    'supplier_order_id', 'product_id',
    'unit_cost_final', 'quantity', 'notes',
];
// No updated_at: public $timestamps = false; + manually set created_at on create
// Or: const UPDATED_AT = null;
```

#### `SupplierOrderTracking`
```php
protected $fillable = ['supplier_order_id', 'tracking'];
// const UPDATED_AT = null;
```

#### `SupplierOrderPayment`
```php
protected $fillable = ['supplier_order_id', 'amount', 'paid_at'];

protected function casts(): array {
    return ['paid_at' => 'date:Y-m-d'];
}
```

#### `SupplierOrderStatus`
```php
protected $fillable = ['name', 'description', 'sort_order'];
```

### TypeScript Interfaces

```ts
// resources/js/features/supplier-orders/types/supplierOrders.ts

export interface SupplierOrderStatus {
    id: number
    name: string
    description: string | null
    sort_order: number | null
}

export interface SupplierOrderItem {
    id: number
    supplier_order_id: number
    product_id: number
    unit_cost_final: number | string
    quantity: number
    notes: string | null
    created_at: string
    product?: Product | null
}

export interface SupplierOrderTracking {
    id: number
    supplier_order_id: number
    tracking: string
    created_at: string
}

export interface SupplierOrderPayment {
    id: number
    supplier_order_id: number
    amount: number | string
    paid_at: string
}

export interface SupplierOrder {
    id: number
    supplier_id: number
    status_id: number
    order_number: string
    ordered_at: string | null
    shipped_at: string | null
    arrived_at: string | null
    created_at: string
    updated_at: string
    supplier?: Supplier | null
    status?: SupplierOrderStatus | null
    items?: SupplierOrderItem[]
    trackings?: SupplierOrderTracking[]
    payments?: SupplierOrderPayment[]
    items_count?: number
}
```

### Routes

```php
// routes/web.php — under auth middleware
Route::resource('supplier-orders', SupplierOrderController::class)->except(['show']);
Route::post('supplier-orders/{supplier_order}/split', [SupplierOrderController::class, 'split'])
    ->name('supplier-orders.split');
Route::patch('supplier-orders/{supplier_order}/status', [SupplierOrderController::class, 'updateStatus'])
    ->name('supplier-orders.status');

Route::get('supplier-order-statuses', [SupplierOrderStatusController::class, 'index'])
    ->name('supplier-order-statuses.index');
Route::post('supplier-order-statuses', [SupplierOrderStatusController::class, 'store'])
    ->name('supplier-order-statuses.store');
Route::put('supplier-order-statuses/{status}', [SupplierOrderStatusController::class, 'update'])
    ->name('supplier-order-statuses.update');
Route::delete('supplier-order-statuses/{status}', [SupplierOrderStatusController::class, 'destroy'])
    ->name('supplier-order-statuses.destroy');
Route::patch('supplier-order-statuses/reorder', [SupplierOrderStatusController::class, 'reorder'])
    ->name('supplier-order-statuses.reorder');

Route::post('supplier-orders/{supplier_order}/trackings', [SupplierOrderTrackingController::class, 'store'])
    ->name('supplier-orders.trackings.store');

Route::post('supplier-orders/{supplier_order}/payments', [SupplierOrderPaymentController::class, 'store'])
    ->name('supplier-orders.payments.store');
Route::delete('supplier-orders/{supplier_order}/payments/{payment}', [SupplierOrderPaymentController::class, 'destroy'])
    ->name('supplier-orders.payments.destroy');
```

### Order Split Logic

The `split` action runs inside a DB transaction:

1. Validate: `items_to_move` (array of item IDs, min 1) and `new_order_number` (required string)
2. Validate that remaining items (not in `items_to_move`) count ≥ 1
3. Create new `SupplierOrder` copying `supplier_id` and `status_id` from original; set `order_number` to user input
4. Move selected `SupplierOrderItem` records to the new order (`UPDATE supplier_order_items SET supplier_order_id = ? WHERE id IN (?)`)
5. Redirect to `supplier-orders.edit` for the new order

### Index N+1 Prevention

```php
SupplierOrder::query()
    ->with(['supplier:id,name', 'status:id,name'])
    ->withCount('items')
    ->latest('id')
    ->get();
```

The `index` action also passes `statuses` (all `SupplierOrderStatus` records ordered by `sort_order`) as an Inertia prop so the inline status `Select` in `Index.vue` can be populated without a separate request.

### Inline Status Update

`UpdateStatusRequest` (`app/Http/Requests/SupplierOrders/UpdateStatusRequest.php`) validates that `status_id` exists in the `supplier_order_statuses` table. The `updateStatus` controller action updates the order's `status_id` and redirects back to `supplier-orders.index`.

### Split Order Visual Indicator

The index query adds a computed flag. Orders sharing an `order_number` prefix (before any `-split-` suffix) or having the same base number are grouped. The simplest approach: the controller passes a `splitGroups` map (keyed by `order_number`) so the frontend can render a badge. Alternatively, a `is_split` boolean can be derived in the controller by detecting duplicate `order_number` prefixes in the result set.

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Order creation requires all mandatory fields

*For any* attempt to create a `SupplierOrder` missing one or more required fields (supplier, order_number, status, or items), the system should reject the request with validation errors and the order count in the database should remain unchanged.

**Validates: Requirements 1.2, 1.5**

---

### Property 2: Optional date fields are accepted with or without values

*For any* `SupplierOrder` creation or update, the system should accept the record regardless of whether `ordered_at`, `shipped_at`, and `arrived_at` are provided or null.

**Validates: Requirements 1.3**

---

### Property 3: Cascade delete removes all child records

*For any* `SupplierOrder` that has associated items, trackings, and payments, deleting the order should result in zero remaining items, trackings, and payments for that order in the database.

**Validates: Requirements 1.6**

---

### Property 4: Item validation rejects invalid cost and quantity

*For any* `SupplierOrderItem` where `unit_cost_final` ≤ 0 or `quantity` ≤ 0 or `product_id` is absent, the system should reject the item and not persist the order.

**Validates: Requirements 2.2, 2.6**

---

### Property 5: Item `created_at` is immutable after creation

*For any* `SupplierOrderItem`, updating the parent order or any sibling item should leave that item's `created_at` timestamp unchanged.

**Validates: Requirements 2.7**

---

### Property 6: Status name uniqueness is enforced

*For any* two `SupplierOrderStatus` records, the system should reject creation of the second if its `name` is identical (case-insensitively) to the first, and the status count should remain unchanged.

**Validates: Requirements 3.3**

---

### Property 7: In-use status cannot be deleted

*For any* `SupplierOrderStatus` that is referenced by at least one `SupplierOrder`, a delete request should be rejected and the status should remain in the database.

**Validates: Requirements 3.4**

---

### Property 8: Status reorder persists new sort_order values

*For any* permutation of `SupplierOrderStatus` IDs submitted to the reorder endpoint, querying statuses afterward should return them in the submitted order (ascending by `sort_order`).

**Validates: Requirements 3.6**

---

### Property 9: Tracking entries are append-only

*For any* `SupplierOrder` with N tracking entries, adding a new tracking number should result in exactly N+1 entries, with all previous entries still present.

**Validates: Requirements 4.2**

---

### Property 10: Tracking requires a non-empty value

*For any* tracking submission where the `tracking` field is empty or whitespace-only, the system should reject the request and the tracking count for the order should remain unchanged.

**Validates: Requirements 4.3**

---

### Property 11: Trackings are returned in chronological order

*For any* `SupplierOrder` with multiple tracking entries, the list returned by the edit page should be sorted ascending by `created_at`.

**Validates: Requirements 4.4**

---

### Property 12: Split preserves original order number and remaining items

*For any* `SupplierOrder` with items [A, B, C], splitting off item [C] should leave the original order with its unchanged `order_number` and items [A, B].

**Validates: Requirements 5.2**

---

### Property 13: Split requires a new order number

*For any* split request that omits `new_order_number` or provides an empty value, the system should reject the request and no new order should be created.

**Validates: Requirements 5.3**

---

### Property 14: Split copies supplier and status to new order

*For any* split operation, the newly created `SupplierOrder` should have the same `supplier_id` and `status_id` as the original order.

**Validates: Requirements 5.4**

---

### Property 15: Split requires at least one item on each side

*For any* split request where all items are selected to move (leaving the original empty) or no items are selected (leaving the new order empty), the system should reject the request and no new order should be created.

**Validates: Requirements 5.6**

---

### Property 16: Payment validation rejects invalid amount or missing date

*For any* payment submission where `amount` ≤ 0 or `paid_at` is absent, the system should reject the request and the payment count for the order should remain unchanged.

**Validates: Requirements 6.3**

---

### Property 17: Payments are returned sorted by paid_at ascending

*For any* `SupplierOrder` with multiple payments, the list returned by the edit page should be sorted ascending by `paid_at`.

**Validates: Requirements 6.5**

---

### Property 18: Payment deletion is isolated

*For any* `SupplierOrder` with N payments, deleting one specific payment should result in exactly N-1 payments, with all other payments and the order itself unchanged.

**Validates: Requirements 6.6**

---

### Property 19: Inline status update persists and redirects

*For any* valid `status_id` submitted to `PATCH /supplier-orders/{order}/status`, the `SupplierOrder`'s `status_id` should be updated to the submitted value and the response should redirect to the index.

**Validates: Requirements 7.4**

---

## Error Handling

### Validation Errors
- All validation is handled by Form Requests; Laravel returns 422 with field-level errors consumed by Inertia's `useForm` on the frontend.
- The `split` action uses a custom `SplitRequest` that validates both `items_to_move` and `new_order_number`, plus a manual check that remaining items ≥ 1.

### Status Delete Protection
- `SupplierOrderStatusController@destroy` checks `$status->supplierOrders()->exists()` before deleting; returns a 422 with an error message if in use.

### Split Transaction Safety
- The split runs inside `DB::transaction()`; any failure rolls back both the new order creation and the item reassignment.

### Cascade Deletes
- Enforced at the database level via `onDelete('cascade')` in migrations, not just at the Eloquent level, ensuring integrity even from raw queries.

### Not Found
- Laravel's route model binding automatically returns 404 for unknown IDs on all parameterised routes.

---

## Testing Strategy

### Dual Testing Approach

Both unit/feature tests and property-based tests are used. Feature tests cover specific HTTP behaviors, redirects, auth, and persistence. Property tests verify universal invariants across randomized inputs.

### PHP Testing (Pest 4.4)

**Feature tests** (`tests/Feature/SupplierOrders/`) cover:
- Each CRUD endpoint returns correct HTTP status for authenticated and unauthenticated users
- Specific examples: creating an order with valid data persists correctly; index returns expected fields
- Edge cases: split with all items selected is rejected; status delete when in use is rejected
- Integration: cascade delete removes all child records

**Property-based tests** use [Pest's `dataset` + randomized factories] or a PBT library. Since the PHP ecosystem's primary PBT option is `eris/eris`, and the project uses Pest, the recommended approach is **`giorgiopogliani/pest-plugin-faker`** combined with randomized factory loops (100 iterations minimum per property) to approximate property testing within Pest's familiar syntax.

Each property test is tagged with a comment referencing the design property:
```php
// Feature: supplier-orders, Property 1: Order creation requires all mandatory fields
test('rejects order creation when required fields are missing', function () {
    // 100 iterations with randomized missing fields
});
```

**Property test configuration:**
- Minimum 100 iterations per property test
- Each property test references its design document property in a comment
- Tag format: `// Feature: supplier-orders, Property {N}: {property_text}`
- Each correctness property is implemented by a single property-based test

### Test File Structure

```
tests/
├── Feature/
│   └── SupplierOrders/
│       ├── SupplierOrderCrudTest.php        — Properties 1, 2, 3
│       ├── SupplierOrderItemTest.php        — Properties 4, 5
│       ├── SupplierOrderStatusTest.php      — Properties 6, 7, 8
│       ├── SupplierOrderTrackingTest.php    — Properties 9, 10, 11
│       ├── SupplierOrderSplitTest.php       — Properties 12, 13, 14, 15
│       ├── SupplierOrderPaymentTest.php     — Properties 16, 17, 18
│       └── SupplierOrderInlineStatusTest.php — Property 19
```

### Frontend Testing

No frontend test runner is currently configured. TypeScript correctness is verified via `npm run typecheck`. UI behavior is validated manually or through future E2E tests.
