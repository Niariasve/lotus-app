# Implementation Plan: Supplier Orders

## Overview

Implement the supplier order management feature following the existing Laravel/Inertia/Vue 3 patterns. The work is split into: fixing the existing migrations, creating models, form requests, controllers, routes, frontend types/pages/components, and tests.

The key structural change from a naive implementation is that items are always added ungrouped first. Grouping is a separate, explicit action the user takes from the order detail view by selecting items and creating a named group. Grouped and ungrouped items coexist in the same unified list.

## Tasks

- [x] 1. Fix the `supplier_order_items` migration and verify existing migrations
  - Open `database/migrations/2026_03_22_220352_create_supplier_order_items_table.php` and apply the following corrections:
    - Rename column `unit_cost_final` → `unit_cost` (decimal 10,2, no default)
    - Add nullable `group_id` FK column → `supplier_order_item_groups.id` with `SET NULL` on delete (add after `supplier_order_id`)
    - Change `product_id` FK from `cascadeOnDelete()` to `restrictOnDelete()` to prevent orphaned items
    - Fix the `down()` method: `dropIfExists('supplier_order_items')` (currently has a typo)
  - Verify `2026_03_22_210126_create_supplier_orders_table.php` references `supplier_order_statuses` as the FK table
  - _Requirements: 3.1, 3.6, 3.7_

- [ ] 2. Create the `supplier_order_item_groups` migration
  - Run `php artisan make:migration create_supplier_order_item_groups_table --no-interaction`
  - Columns: `id`, `supplier_order_id` FK → `supplier_orders.id` CASCADE DELETE, `name` varchar(150) NOT NULL, timestamps
  - This migration must run **before** `supplier_order_items` so the FK in items can reference it
  - _Requirements: 4.1_

- [x] 3. Create Eloquent models
  - [x] 3.1 Create `app/Models/SupplierOrderStatus.php`
    - `$fillable = ['name', 'description']`
    - `orders(): HasMany` → `SupplierOrder::class` via `status_id`
    - _Requirements: 1.1_

  - [x] 3.2 Create `app/Models/SupplierOrder.php`
    - `$fillable` for all order fields
    - `casts()` for `supplier_id`, `status_id` (integer), and date fields
    - `supplier(): BelongsTo`, `status(): BelongsTo` (via `status_id`), `items(): HasMany`, `groups(): HasMany`
    - _Requirements: 2.1, 2.2_

  - [x] 3.3 Create `app/Models/SupplierOrderItem.php`
    - `$fillable = ['supplier_order_id', 'product_id', 'group_id', 'quantity', 'unit_cost']`
    - `casts()` for `quantity` (integer), `unit_cost` (decimal:2), `group_id` (integer)
    - `order(): BelongsTo`, `product(): BelongsTo`, `group(): BelongsTo` → `SupplierOrderItemGroup::class` via `group_id`
    - _Requirements: 3.1_

  - [ ] 3.4 Create `app/Models/SupplierOrderItemGroup.php`
    - `$fillable = ['supplier_order_id', 'name']`
    - `order(): BelongsTo` → `SupplierOrder::class` via `supplier_order_id`
    - `items(): HasMany` → `SupplierOrderItem::class` via `group_id`
    - _Requirements: 4.1_

- [x] 4. Create Form Requests
  - [x] 4.1 Create `app/Http/Requests/SupplierOrderStatuses/StoreRequest.php`
    - Rules: `name` required|string|max:150|unique:supplier_order_statuses, `description` nullable|string
    - _Requirements: 1.1, 1.2_

  - [x] 4.2 Create `app/Http/Requests/SupplierOrderStatuses/UpdateRequest.php`
    - Same as StoreRequest but unique rule ignores current record
    - _Requirements: 1.1, 1.2_

  - [x] 4.3 Create `app/Http/Requests/SupplierOrders/StoreRequest.php`
    - Rules: `order_number`, `supplier_id`, `status_id`, `tracking`, date fields, nested `items[]`
    - Items do NOT accept a `group_id` — new items are always ungrouped
    - _Requirements: 2.1, 2.3, 2.4, 3.1–3.4, 3.7_

  - [x] 4.4 Create `app/Http/Requests/SupplierOrders/UpdateRequest.php`
    - Same as StoreRequest but `order_number` unique rule ignores current record
    - Items do NOT accept a `group_id` — updated item sets are always ungrouped
    - _Requirements: 5.1, 5.3_

  - [ ] 4.5 Create `app/Http/Requests/SupplierOrderItemGroups/StoreRequest.php`
    - Rules: `name` required|string|max:150, `item_ids` required|array|min:1, `item_ids.*` integer|exists:supplier_order_items,id
    - The controller will additionally verify all item_ids belong to the same order
    - _Requirements: 4.1, 4.3, 4.8_

  - [ ] 4.6 Create `app/Http/Requests/SupplierOrderItemGroups/UpdateRequest.php`
    - Rules: `name` required|string|max:150
    - _Requirements: 4.7, 4.8_

  - [ ] 4.7 Create `app/Http/Requests/SupplierOrderItems/UpdateRequest.php`
    - Rules: `group_id` nullable|integer|exists:supplier_order_item_groups,id
    - The controller will additionally verify the group belongs to the same order
    - _Requirements: 4.5_

- [x] 5. Create controllers and register routes
  - [x] 5.1 Create `app/Http/Controllers/SupplierOrderStatusController.php`
    - `index()`, `store(StoreRequest)`, `update(UpdateRequest, SupplierOrderStatus)`, `destroy(SupplierOrderStatus)`
    - `destroy()`: delete status (DB SET NULL handles orders), flash, redirect back
    - _Requirements: 1.1, 1.3_

  - [x] 5.2 Create `app/Http/Controllers/SupplierOrderController.php`
    - `index()`: paginated orders with eager-loaded `supplier`, `status`, `items_count`; pass all statuses as prop
    - `create()`: render `supplier-orders/Create` with suppliers and statuses
    - `store(StoreRequest)`: `DB::transaction()` — create order then `items()->createMany()` (all ungrouped); flash, redirect to index
    - `show(SupplierOrder)`: load order with `items.product`, `items.group`, `groups`; pass computed `order_total`
    - `edit(SupplierOrder)`: render `supplier-orders/Edit` with order (including items), suppliers, statuses
    - `update(UpdateRequest, SupplierOrder)`: `DB::transaction()` — update order, delete existing items, recreate from request (all ungrouped); flash, redirect to index
    - `destroy(SupplierOrder)`: delete (cascade handles items and groups), flash, redirect to index
    - _Requirements: 2.1, 3.5, 5.1–5.5, 6.1, 6.2, 7.1, 7.2_

  - [ ] 5.3 Create `app/Http/Controllers/SupplierOrderItemGroupController.php`
    - `store(StoreRequest, SupplierOrder)`: `DB::transaction()` — create group, then update all specified items' `group_id`; validate item_ids belong to the order; flash, redirect back
    - `update(UpdateRequest, SupplierOrder, SupplierOrderItemGroup)`: rename group; flash, redirect back
    - `destroy(SupplierOrder, SupplierOrderItemGroup)`: `DB::transaction()` — set `group_id = null` on all group's items, then delete group; flash, redirect back
    - _Requirements: 4.1, 4.2, 4.6, 4.7_

  - [ ] 5.4 Create `app/Http/Controllers/SupplierOrderItemController.php`
    - `store(StoreRequest, SupplierOrder)`: add a single item to an existing order (ungrouped); flash, redirect back
    - `update(UpdateRequest, SupplierOrder, SupplierOrderItem)`: update `group_id` only (move item to group or ungroup); validate group belongs to same order; flash, redirect back
    - `destroy(SupplierOrder, SupplierOrderItem)`: remove item from order; flash, redirect back
    - _Requirements: 3.5, 4.5_

  - [x] 5.5 Register routes in `routes/web.php`
    - Add `use` statements for all new controllers
    - Auth-middleware group with:
      - `Route::resource('supplier-order-statuses', ...)` only index/store/update/destroy
      - `Route::resource('supplier-orders', ...)`
      - `Route::resource('supplier-orders.item-groups', SupplierOrderItemGroupController::class)->only(['store', 'update', 'destroy'])->scoped()`
      - `Route::resource('supplier-orders.items', SupplierOrderItemController::class)->only(['store', 'update', 'destroy'])->scoped()`
    - _Requirements: 8.1, 8.2_

  - [x] 5.6 Regenerate Wayfinder after routes are registered
    - Run `composer run wayfinder:generate`
    - _Requirements: 8.2_

- [ ] 6. Checkpoint — ensure migrations and backend compile cleanly
  - Run `php artisan migrate` to verify all migrations apply without errors
  - Run `composer run analyse` to check for PHPStan issues in new PHP files
  - Ask the user if any questions arise before proceeding to the frontend

- [x] 7. Add i18n keys
  - [x] 7.1 Add keys to `lang/en.json` including new group-related keys:
    - `supplier_orders.fields.group`, `supplier_orders.no_group`
    - `supplier_orders.item_groups.title`, `.create`, `.name`, `.ungroup`
    - _Requirements: 5.2, 5.5_

  - [x] 7.2 Add the same keys to `lang/es.json` with Spanish translations
    - _Requirements: 5.2, 5.5_

- [x] 8. Create TypeScript types and re-exports
  - [x] 8.1 Create `resources/js/features/supplier-order-statuses/types/supplierOrderStatuses.ts`
    - Export `SupplierOrderStatus` interface
    - _Requirements: 1.4_

  - [ ] 8.2 Update `resources/js/features/supplier-orders/types/supplierOrders.ts`
    - Add `SupplierOrderItemGroup` interface (id, supplier_order_id, name, created_at, updated_at)
    - Update `SupplierOrderItem` interface to include `group_id: number | null` and `group?: SupplierOrderItemGroup | null`
    - Update `SupplierOrder` interface to include `groups?: SupplierOrderItemGroup[]`
    - _Requirements: 4.1, 5.2_

  - [x] 8.3 Create `resources/js/features/supplier-orders/types/columns.ts`
    - TanStack column definitions for the orders index table (unchanged from original)
    - _Requirements: 5.1, 5.4_

  - [x] 8.4 Re-export new types from `resources/js/types/index.ts`
    - _Requirements: 5.1_

- [x] 9. Build the StatusDrawer component and orders Index page
  - [x] 9.1 Create `resources/js/pages/supplier-orders/StatusDrawer.vue`
    - _Requirements: 1.1, 1.3, 1.4_

  - [x] 9.2 Create `resources/js/pages/supplier-orders/Index.vue`
    - _Requirements: 1.4, 5.1, 5.4_

  - [x] 9.3 Add actions column to `resources/js/features/supplier-orders/types/columns.ts`
    - _Requirements: 5.1, 5.2, 6.1_

  - [x] 9.4 Create `resources/js/features/supplier-orders/components/SupplierOrderDataTableDropdown.vue`
    - _Requirements: 5.2, 6.1, 6.2_

- [x] 10. Build the Create and Edit order pages
  - [x] 10.1 Create `resources/js/pages/supplier-orders/Create.vue`
    - Items section: add/remove rows; no group field — items are always created ungrouped
    - _Requirements: 2.1, 3.1, 3.7_

  - [x] 10.2 Create `resources/js/pages/supplier-orders/Edit.vue`
    - Items section: pre-populate existing items; no group field — updating items resets them to ungrouped
    - _Requirements: 5.1, 5.2, 3.5_

- [ ] 11. Build the Show order page with unified item list and group management
  - [ ] 11.1 Create `resources/js/pages/supplier-orders/Show.vue`
    - Props: `order: SupplierOrder` (with items including product and group, and groups array with their items), `order_total: number`
    - Display order header fields (supplier, status with null guard, tracking, dates) and order total
    - **Ungrouped items section**: table of items where `group_id` is null; rows have checkboxes; when ≥ 1 item is selected a "Create Group" button appears in the section header; "Create Group" opens a dialog asking for a group name, then submits selected item IDs + name to `supplier-orders.item-groups.store`
    - **Groups section**: one block per group, each with its own item table showing only that group's items (product name, quantity, unit_cost, line total); each block header shows the group name and has rename/delete actions
    - Each item row in a group block has an `ItemGroupAssignDropdown` to move it to another group or back to ungrouped
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 5.2, 5.3, 5.4, 5.5_

  - [ ] 11.2 Create `resources/js/features/supplier-orders/components/ItemGroupBlock.vue`
    - Props: `group: SupplierOrderItemGroup`, `items: SupplierOrderItem[]`, `allGroups: SupplierOrderItemGroup[]`, `order: SupplierOrder`
    - Renders a collapsible block with the group name in the header, plus rename and delete action buttons
    - Contains an item table for the group's items (product name, quantity, unit_cost, line total)
    - Each item row has an `ItemGroupAssignDropdown` to move it to another group or back to ungrouped
    - Rename: inline edit form that submits to `supplier-orders.item-groups.update`
    - Delete: confirmation then submits to `supplier-orders.item-groups.destroy` (items return to ungrouped list)
    - _Requirements: 4.5, 4.6, 4.7, 4.8, 4.9, 5.5_

  - [ ] 11.3 Create `resources/js/features/supplier-orders/components/ItemGroupAssignDropdown.vue`
    - Props: `item: SupplierOrderItem`, `groups: SupplierOrderItemGroup[]`, `order: SupplierOrder`
    - Dropdown options: one entry per group (excluding the item's current group) + "Move to ungrouped" option
    - On select: submits PATCH to `supplier-orders.items.update` with the new `group_id` (or null to ungroup)
    - _Requirements: 4.5, 4.6_

- [ ] 12. Checkpoint — frontend typecheck and lint
  - [ ] Run `npm run typecheck` to verify no TypeScript errors across new pages and components
  - [ ] Run `npm run lint:check` to verify ESLint passes
  - [ ] Ask the user if any questions arise before proceeding to tests

- [x] 13. Write feature tests for SupplierOrderStatus
  - [x] 13.1 Create `tests/Feature/SupplierOrderStatuses/SupplierOrderStatusTest.php`
    - `test('guests are redirected from status routes')` — covers Property 15
    - `test('authenticated user can create a status')` — covers 1.1
    - `test('duplicate status name is rejected')` — covers Property 1
    - `test('deleting a status nullifies order status_id')` — covers Property 2
    - `test('status index returns all statuses')` — covers 1.4

  - [x]* 13.2 Write property test for status name uniqueness (Property 1)
  - [x]* 13.3 Write property test for status deletion nullifying orders (Property 2)

- [x] 14. Write feature tests for SupplierOrder
  - [x] 14.1 Create `tests/Feature/SupplierOrders/SupplierOrderTest.php`
    - `test('guests are redirected from order routes')` — covers Property 15
    - `test('order requires valid supplier_id')` — covers Property 3
    - `test('order rejects invalid status_id')` — covers Property 4
    - `test('order items are validated')` — covers Properties 5 and 6
    - `test('order store persists order and items ungrouped')` — covers 2.1, 3.1, Property 7
    - `test('order update syncs items and ungroups them')` — covers Property 8
    - `test('deleting order cascades to items and groups')` — covers Property 12
    - `test('order index returns expected shape')` — covers 5.1
    - `test('order show returns ungrouped and grouped items in separate sections')` — covers 5.2, Properties 13 and 14
    - `test('null status displays without error')` — covers 5.4
    - `test('delete redirects to index with success')` — covers 7.2

  - [x]* 14.2 Write property test for item constraints (Property 5)
  - [x]* 14.3 Write property test for valid product reference (Property 6)
  - [x]* 14.4 Write property test for new items always ungrouped (Property 7)
  - [x]* 14.5 Write property test for order update item sync (Property 8)
  - [x]* 14.6 Write property test for order deletion cascade (Property 12)
  - [x]* 14.7 Write property test for line total computation (Property 13)
  - [x]* 14.8 Write property test for order total computation (Property 14)
  - [x]* 14.9 Write property test for authentication enforcement (Property 15)

- [ ] 15. Write feature tests for SupplierOrderItemGroup
  - [ ] 15.1 Create `tests/Feature/SupplierOrders/SupplierOrderItemGroupTest.php`
    - `test('guests are redirected from item group routes')` — covers Property 15, _Requirements: 8.1, 8.2_
    - `test('creating a group removes items from ungrouped list')` — covers Property 9, _Requirements: 4.1, 4.2_
    - `test('creating a group requires at least one item')` — covers 4.3
    - `test('creating a group requires a name')` — covers 4.9
    - `test('item_ids must belong to the same order')` — covers 4.1 (cross-order item rejection)
    - `test('deleting a group returns its items to the ungrouped list')` — covers Property 10, _Requirements: 4.7_
    - `test('renaming a group persists the new name')` — covers 4.8
    - `test('moving an item to another group removes it from the source group list')` — covers Property 11, _Requirements: 4.5_
    - `test('ungrouping an item returns it to the ungrouped list')` — covers Property 11, _Requirements: 4.6_

  - [ ]* 15.2 Write property test for group creation item assignment (Property 9)
    - Loop 30 iterations: create order with N items, select random subset, create group, assert all selected items have correct group_id
  - [ ]* 15.3 Write property test for group deletion ungrouping (Property 10)
    - Loop 30 iterations: create group with N items, delete group, assert all items still exist with group_id = null
  - [ ]* 15.4 Write property test for item group assignment mutability (Property 11)
    - Loop 50 iterations: create item, assign to group, reassign to another group, ungroup — assert each state persists correctly

- [ ] 16. Final checkpoint — run full test suite
  - [ ] Run `php artisan test tests/Feature/SupplierOrders/ tests/Feature/SupplierOrderStatuses/` and ensure all tests pass
  - [ ] Run `composer run analyse` to confirm no PHPStan regressions
  - [ ] Ask the user if any questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- Items are **always created ungrouped** — neither `StoreRequest` nor `UpdateRequest` for orders accept a `group_id` on items
- Grouping is a separate action: user selects items on the Show page → clicks "Create Group" → names the group
- The unified item list on Show.vue renders all items (grouped and ungrouped) in one flat table; group membership is shown as a badge/label per row
- The `supplier_order_item_groups` migration must be created and run **before** the `supplier_order_items` migration
- `store` and `update` on `SupplierOrderController` must use `DB::transaction()` to ensure atomicity
- `store` and `destroy` on `SupplierOrderItemGroupController` must use `DB::transaction()` to ensure atomicity
- Wayfinder must be regenerated (task 5.6) before writing any frontend code that imports from `@/routes/supplier-orders` or related helpers
- Property tests use Faker loops (no external PBT library required)
