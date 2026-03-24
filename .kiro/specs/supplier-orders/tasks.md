# Implementation Plan: Supplier Orders

## Overview

Implement the supplier order management feature following the existing Laravel/Inertia/Vue 3 patterns. The work is split into: fixing the existing `supplier_order_items` migration, creating models, form requests, controllers, routes, frontend types/pages/components, and tests.

## Tasks

- [x] 1. Fix the `supplier_order_items` migration and verify existing migrations
  - Open `database/migrations/2026_03_22_220352_create_supplier_order_items_table.php` and apply the following corrections:
    - Rename column `unit_cost_final` → `unit_cost` (decimal 10,2, no default)
    - Change `product_id` FK from `cascadeOnDelete()` to `restrictOnDelete()` to prevent orphaned items
    - Fix the `down()` method: `dropIfExists('supplier_order_items')` (currently has a typo: `supplier_order_item`)
  - Verify `2026_03_22_210126_create_supplier_orders_table.php` references `supplier_order_statuses` (not `supplier_orders_statuses`) as the FK table — it already does, no change needed
  - _Requirements: 3.1, 3.6_

- [x] 2. Create Eloquent models
  - [x] 2.1 Create `app/Models/SupplierOrderStatus.php`
    - `$fillable = ['name', 'description']`
    - `orders(): HasMany` → `SupplierOrder::class` via `status_id`
    - _Requirements: 1.1_

  - [x] 2.2 Create `app/Models/SupplierOrder.php`
    - `$fillable` for all order fields
    - `casts()` for `supplier_id`, `status_id` (integer), and date fields (`ordered_at`, `shipped_at`, `arrived_at`)
    - `supplier(): BelongsTo`, `status(): BelongsTo` (via `status_id`), `items(): HasMany`
    - _Requirements: 2.1, 2.2_

  - [x] 2.3 Create `app/Models/SupplierOrderItem.php`
    - `$fillable = ['supplier_order_id', 'product_id', 'quantity', 'unit_cost']`
    - `casts()` for `quantity` (integer) and `unit_cost` (decimal:2)
    - `order(): BelongsTo`, `product(): BelongsTo`
    - _Requirements: 3.1_

- [x] 3. Create Form Requests
  - [x] 3.1 Create `app/Http/Requests/SupplierOrderStatuses/StoreRequest.php`
    - Rules: `name` required|string|max:150|unique:supplier_order_statuses, `description` nullable|string
    - _Requirements: 1.1, 1.2_

  - [x] 3.2 Create `app/Http/Requests/SupplierOrderStatuses/UpdateRequest.php`
    - Same as StoreRequest but unique rule ignores current record: `unique:supplier_order_statuses,name,{id}`
    - _Requirements: 1.1, 1.2_

  - [x] 3.3 Create `app/Http/Requests/SupplierOrders/StoreRequest.php`
    - Rules: `order_number` required|string|unique:supplier_orders, `supplier_id` required|integer|exists:suppliers,id, `status_id` nullable|integer|exists:supplier_order_statuses,id, `tracking` nullable|string|max:1000, `ordered_at` nullable|date, `shipped_at` nullable|date, `arrived_at` required|date
    - Nested items array: `items` required|array|min:1, `items.*.product_id` required|integer|exists:products,id, `items.*.quantity` required|integer|min:1, `items.*.unit_cost` required|decimal:0,2|min:0
    - _Requirements: 2.1, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4_

  - [x] 3.4 Create `app/Http/Requests/SupplierOrders/UpdateRequest.php`
    - Same as StoreRequest but `order_number` unique rule ignores current record
    - _Requirements: 5.1, 5.3_

- [x] 4. Create controllers and register routes
  - [x] 4.1 Create `app/Http/Controllers/SupplierOrderStatusController.php`
    - `index()`: return all statuses (used as JSON/Inertia prop for the drawer)
    - `store(StoreRequest $request)`: create status, flash success, redirect to `supplier-orders.index`
    - `update(UpdateRequest $request, SupplierOrderStatus $supplierOrderStatus)`: update, flash, redirect back
    - `destroy(SupplierOrderStatus $supplierOrderStatus)`: delete (DB SET NULL handles orders), flash, redirect back
    - _Requirements: 1.1, 1.3_

  - [x] 4.2 Create `app/Http/Controllers/SupplierOrderController.php`
    - `index()`: paginated orders with eager-loaded `supplier`, `status`, `items_count`; also pass all `SupplierOrderStatus` records as `statuses` prop
    - `create()`: render `supplier-orders/Create` with all suppliers and statuses as props
    - `store(StoreRequest $request)`: wrap in `DB::transaction()` — create order then `items()->createMany()`; flash success, redirect to index
    - `show(SupplierOrder $supplierOrder)`: load order with `items.product`; pass computed `order_total` as prop
    - `edit(SupplierOrder $supplierOrder)`: render `supplier-orders/Edit` with order (including items), suppliers, statuses
    - `update(UpdateRequest $request, SupplierOrder $supplierOrder)`: wrap in `DB::transaction()` — update order, delete existing items, recreate from request; flash, redirect to index
    - `destroy(SupplierOrder $supplierOrder)`: delete (cascade handles items), flash, redirect to index
    - _Requirements: 2.1, 3.5, 4.1, 4.2, 4.3, 5.1, 5.2, 6.1, 6.2_

  - [x] 4.3 Register routes in `routes/web.php`
    - Add `use` statements for both new controllers
    - Add auth-middleware group with:
      - `Route::resource('supplier-order-statuses', SupplierOrderStatusController::class)->only(['index', 'store', 'update', 'destroy']);`
      - `Route::resource('supplier-orders', SupplierOrderController::class);`
    - _Requirements: 7.1, 7.2_

  - [x] 4.4 Regenerate Wayfinder after routes are registered
    - Run `composer run wayfinder:generate` to produce `resources/js/routes/supplier-orders.ts`, `resources/js/routes/supplier-order-statuses.ts`, and the corresponding action helpers
    - _Requirements: 7.2_

- [ ] 5. Checkpoint — ensure migrations and backend compile cleanly
  - Run `php artisan migrate` to verify all three migrations apply without errors
  - Run `composer run analyse` to check for PHPStan issues in new PHP files
  - Ask the user if any questions arise before proceeding to the frontend

- [x] 6. Add i18n keys
  - [x] 6.1 Add keys to `lang/en.json`:
    - `supplier_orders.title`, `supplier_orders.description`, `supplier_order_statuses.title`, `supplier_order_statuses.description`
    - `supplier_orders.fields.order_number`, `.supplier`, `.status`, `.tracking`, `.ordered_at`, `.shipped_at`, `.arrived_at`, `.items`, `.unit_cost`, `.quantity`, `.line_total`, `.order_total`
    - `supplier_orders.no_status` → `"—"`
    - _Requirements: 4.1, 4.2, 4.4_

  - [x] 6.2 Add the same keys to `lang/es.json` with Spanish translations (see design.md i18n table for values)
    - _Requirements: 4.1, 4.2, 4.4_

- [x] 7. Create TypeScript types and re-exports
  - [x] 7.1 Create `resources/js/features/supplier-order-statuses/types/supplierOrderStatuses.ts`
    - Export `SupplierOrderStatus` interface (id, name, description, created_at, updated_at)
    - _Requirements: 1.4_

  - [x] 7.2 Create `resources/js/features/supplier-orders/types/supplierOrders.ts`
    - Export `SupplierOrderItem` interface (id, supplier_order_id, product_id, quantity, unit_cost, product?)
    - Export `SupplierOrder` interface (all fields, optional relations: supplier?, status?, items?, items_count?)
    - _Requirements: 4.1, 4.2_

  - [x] 7.3 Create `resources/js/features/supplier-orders/types/columns.ts`
    - TanStack column definitions for the orders index table: order_number, supplier name, status name (with null guard showing "—"), tracking, ordered_at, shipped_at, arrived_at, items_count, created_at
    - _Requirements: 4.1, 4.4_

  - [x] 7.4 Re-export new types from `resources/js/types/index.ts`
    - Add exports for `supplierOrderStatuses` and `supplierOrders` type files
    - _Requirements: 4.1_

- [x] 8. Build the StatusDrawer component and orders Index page
  - [x] 8.1 Create `resources/js/pages/supplier-orders/StatusDrawer.vue`
    - Props: `statuses: SupplierOrderStatus[]`, `open: boolean`; emit `update:open`
    - Uses Reka UI Drawer primitive
    - Lists all statuses; each row has an edit button that switches to an inline edit form (name + description inputs, save/cancel)
    - Has a "New Status" section at the bottom with a create form
    - All form submissions use Inertia form posts to Wayfinder-generated `supplier-order-statuses` routes
    - On success Inertia reloads page props; drawer stays open
    - _Requirements: 1.1, 1.3, 1.4_

  - [x] 8.2 Create `resources/js/pages/supplier-orders/Index.vue`
    - Props: `orders: SupplierOrder[]` (paginated), `statuses: SupplierOrderStatus[]`
    - Header with "Manage Statuses" button (opens StatusDrawer) and "Create Order" button
    - DataTable using `columns` from `columns.ts`
    - Passes `statuses` and drawer open state to `StatusDrawer`
    - _Requirements: 1.4, 4.1, 4.4_

  - [x] 8.3 Add a dedicated actions column to `resources/js/features/supplier-orders/types/columns.ts`
    - Include an `actions` column aligned with the other table implementations in the repo
    - Render a supplier-order table actions component instead of keeping row actions inline in the page
    - Keep the existing data columns intact while appending the new actions column
    - _Requirements: 4.1, 4.2, 5.1_

  - [x] 8.4 Create `resources/js/features/supplier-orders/components/SupplierOrderDataTableDropdown.vue`
    - Match the interaction pattern used by the other resource tables
    - Provide row actions for at least:
      - View order (`supplier-orders.show`)
      - Edit order (`supplier-orders.edit`)
      - Delete order (`supplier-orders.destroy`) with confirmation dialog
    - Use Wayfinder-generated supplier-order routes for both actions
    - _Requirements: 4.2, 5.1, 6.1, 6.2_

- [x] 9. Build the Create and Edit order pages
  - [x] 9.1 Create `resources/js/pages/supplier-orders/Create.vue`
    - Props: `suppliers: Supplier[]`, `statuses: SupplierOrderStatus[]`
    - Form fields: order_number, supplier_id (Select), status_id (Select, optional), tracking, ordered_at, shipped_at, arrived_at
    - Dynamic items section: add/remove rows, each with product_id (Select), quantity, unit_cost; show computed line total per row
    - Submit via Inertia form POST to `supplier-orders.store`
    - _Requirements: 2.1, 3.1_

  - [x] 9.2 Create `resources/js/pages/supplier-orders/Edit.vue`
    - Props: `order: SupplierOrder` (with items), `suppliers: Supplier[]`, `statuses: SupplierOrderStatus[]`
    - Pre-populate all fields including existing items
    - Submit via Inertia form PUT to `supplier-orders.update`
    - _Requirements: 5.1, 5.2, 3.5_

- [x] 10. Build the Show order page
  - [x] Create `resources/js/pages/supplier-orders/Show.vue`
  - [x] Props: `order: SupplierOrder` (with items including product), `order_total: number`
  - [x] Display order header fields (supplier, status with null guard, tracking, dates)
  - [x] Items table: product name, quantity, unit_cost, computed line total (`quantity × unit_cost`)
  - [x] Display order total from prop
  - _Requirements: 4.2, 4.3, 4.4_

- [x] 11. Checkpoint — frontend typecheck and lint
  - [x] Run `npm run typecheck` to verify no TypeScript errors across new pages and components
  - [x] Run `npm run lint:check` to verify ESLint passes
  - [x] Ask the user if any questions arise before proceeding to tests

- [x] 12. Write feature tests for SupplierOrderStatus
  - [x] 12.1 Create `tests/Feature/SupplierOrderStatuses/SupplierOrderStatusTest.php`
    - `test('guests are redirected from status routes')` — GET index, POST store, PATCH update, DELETE destroy all redirect to login; covers Property 11, _Requirements: 7.1, 7.2_
    - `test('authenticated user can create a status')` — POST valid payload, assert DB has record; _Requirements: 1.1_
    - `test('duplicate status name is rejected')` — POST same name twice, assert validation error; covers Property 1, _Requirements: 1.2_
    - `test('deleting a status nullifies order status_id')` — create status + order, delete status, assert order status_id is null; covers Property 2, _Requirements: 1.3_
    - `test('status index returns all statuses')` — assert response contains all created statuses; _Requirements: 1.4_

  - [x]* 12.2 Write property test for status name uniqueness (Property 1)
    - **Property 1: Status name uniqueness**
    - **Validates: Requirements 1.2**
    - Loop 50 iterations: generate random name, create status, attempt duplicate, assert validation error each time

  - [x]* 12.3 Write property test for status deletion nullifying orders (Property 2)
    - **Property 2: Status deletion nullifies order references**
    - **Validates: Requirements 1.3**
    - Loop 20 iterations: create status + N orders assigned to it, delete status, assert all orders have status_id = null

- [x] 13. Write feature tests for SupplierOrder
  - [x] 13.1 Create `tests/Feature/SupplierOrders/SupplierOrderTest.php`
    - `test('guests are redirected from order routes')` — covers Property 11, _Requirements: 7.1, 7.2_
    - `test('order requires valid supplier_id')` — covers Property 3, _Requirements: 2.2, 2.3_
    - `test('order rejects invalid status_id')` — covers Property 4, _Requirements: 2.4_
    - `test('order items are validated')` — quantity=0, unit_cost=-1, bad product_id each rejected; covers Properties 5 and 6, _Requirements: 3.2, 3.3, 3.4_
    - `test('order store persists order and items in transaction')` — POST valid order+items, assert order and items in DB; _Requirements: 2.1, 3.1_
    - `test('order update syncs items')` — covers Property 7, _Requirements: 3.5_
    - `test('deleting order cascades to items')` — covers Property 8, _Requirements: 3.6, 6.1_
    - `test('order index returns expected shape')` — assert paginated response includes supplier name, status, item count; _Requirements: 4.1_
    - `test('order show returns items with line totals')` — assert show response includes items and order_total; _Requirements: 4.2, 4.3_
    - `test('null status displays without error')` — order with status_id=null, assert show response succeeds; _Requirements: 4.4_
    - `test('delete redirects to index with success')` — covers _Requirements: 6.2_

  - [x]* 13.2 Write property test for item constraints (Property 5)
    - **Property 5: Item constraints are enforced**
    - **Validates: Requirements 3.1, 3.3, 3.4**
    - Loop 100 iterations: generate random quantity (including 0 and negatives) and unit_cost (including negatives), assert invalid values are rejected and valid values are accepted

  - [x]* 13.3 Write property test for valid product reference (Property 6)
    - **Property 6: Item references a valid product**
    - **Validates: Requirements 3.2**
    - Loop 50 iterations: submit item with non-existent product_id, assert validation error each time

  - [x]* 13.4 Write property test for order update item sync (Property 7)
    - **Property 7: Order update syncs item collection**
    - **Validates: Requirements 3.5**
    - Loop 20 iterations: create order with N items, update with M different items, assert DB items exactly match submitted set

  - [x]* 13.5 Write property test for order deletion cascade (Property 8)
    - **Property 8: Order deletion cascades to items**
    - **Validates: Requirements 3.6, 6.1**
    - Loop 20 iterations: create order with random number of items, delete order, assert no orphaned items remain

  - [x]* 13.6 Write property test for line total computation (Property 9)
    - **Property 9: Line total computation**
    - **Validates: Requirements 4.2**
    - Loop 100 iterations: generate random quantity (≥1) and unit_cost (≥0), assert `round(quantity * unit_cost, 2)` equals expected

  - [x]* 13.7 Write property test for order total computation (Property 10)
    - **Property 10: Order total computation**
    - **Validates: Requirements 4.3**
    - Loop 100 iterations: generate random set of items, assert order total equals sum of all line totals

  - [x]* 13.8 Write property test for authentication enforcement (Property 11)
    - **Property 11: All routes require authentication**
    - **Validates: Requirements 7.1, 7.2**
    - For each route verb/path combination in both resource groups, assert unauthenticated request redirects to login

- [x] 14. Final checkpoint — run full test suite
  - [x] Run `php artisan test tests/Feature/SupplierOrders/ tests/Feature/SupplierOrderStatuses/` and ensure all tests pass
  - [x] Run `composer run analyse` to confirm no PHPStan regressions
  - [x] Ask the user if any questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- The `supplier_order_items` migration already exists but needs corrections (task 1) before running migrations
- The statuses table is `supplier_order_statuses` — use this name consistently in all `exists:` and `unique:` validation rules
- Wayfinder must be regenerated (task 4.4) before writing any frontend code that imports from `@/routes/supplier-orders` or `@/routes/supplier-order-statuses`
- `store` and `update` on `SupplierOrderController` must use `DB::transaction()` to ensure atomicity
- Property tests use Faker loops (no external PBT library required)
