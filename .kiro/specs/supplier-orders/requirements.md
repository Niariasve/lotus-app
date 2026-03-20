# Requirements Document

## Introduction

The Supplier Orders feature formalizes the purchasing process from a supplier. The typical workflow begins with the User reviewing SupplierProductOffers to identify the best sourcing option for a given Product, then manually creating a SupplierOrder against the chosen supplier. Orders track payment schedules, shipping lifecycle dates, tracking numbers (which accumulate over time), and a user-managed status. Orders may be split: the User selects which items move to a new order, the original order retains its order number with the remaining items, and the new order receives a distinct order number entered manually by the User. Statuses are fully user-managed through a reusable drawer component that will serve other status-driven entities across the project.

## Glossary

- **Product**: A catalog entry representing a physical item sold by the business (identified by SKU, name, brand, dimensions, weight, etc.).
- **Supplier**: A vendor from whom Products are sourced, with associated tax and shipping policies.
- **SupplierProductOffer**: A record linking a specific Supplier to a specific Product, capturing the base cost and availability at a point in time. Used to compare sourcing options before a SupplierOrder is placed; does not automatically populate order fields.
- **SupplierOrder**: A purchase order placed with a specific Supplier, containing one or more SupplierOrderItems.
- **SupplierOrderItem**: A single product line within a SupplierOrder, recording the confirmed unit cost, quantity, and optional notes.
- **SupplierOrderStatus**: A user-defined label describing the current state of a SupplierOrder (e.g. "Pending", "Shipped", "Arrived").
- **SupplierOrderTracking**: A tracking number entry associated with a SupplierOrder; multiple entries may exist and all are persisted.
- **OrderSplit**: The operation of moving a selected subset of SupplierOrderItems from an existing SupplierOrder into a new SupplierOrder. The original order retains its order number; the new order receives a distinct, user-provided order number.
- **StatusDrawer**: A reusable drawer UI component for creating, editing, reordering, and deleting statuses; designed to be used across multiple status-driven entities in the project.
- **Payment**: A monetary installment recorded against a SupplierOrder in the `supplier_order_payments` table; orders may have one or more Payments.
- **System**: The Laravel/Inertia application described in this document.
- **User**: An authenticated operator of the System.

---

## Requirements

### Requirement 1: Supplier Order Management

**User Story:** As a User, I want to create, view, edit, and delete supplier orders, so that I can track purchases made from suppliers.

#### Acceptance Criteria

1. THE System SHALL provide index, create, edit, and delete operations for SupplierOrders.
2. WHEN a User creates a SupplierOrder, THE System SHALL require the User to manually select a Supplier, enter an order number, add at least one SupplierOrderItem, and select a SupplierOrderStatus.
3. WHEN a User creates a SupplierOrder, THE System SHALL allow optional values for `ordered_at`, `shipped_at`, and `arrived_at` dates.
4. THE System SHALL store `ordered_at`, `shipped_at`, and `arrived_at` as nullable date fields on the SupplierOrder.
5. WHEN a User submits a SupplierOrder with missing required fields, THE System SHALL return field-level validation errors without persisting the record.
6. WHEN a User deletes a SupplierOrder, THE System SHALL cascade-delete all associated SupplierOrderItems, SupplierOrderTrackings, and Payments.

---

### Requirement 2: Supplier Order Items

**User Story:** As a User, I want to add multiple products to a supplier order with quantity and notes, so that a single order can cover several products from the same supplier with full line-item detail.

#### Acceptance Criteria

1. THE System SHALL allow a SupplierOrder to contain one or more SupplierOrderItems.
2. WHEN a User adds a SupplierOrderItem, THE System SHALL require a Product, a `unit_cost_final` value greater than zero, and a `quantity` integer greater than zero.
3. WHEN a User adds a SupplierOrderItem, THE System SHALL allow an optional `notes` text field on the SupplierOrderItem.
4. THE System SHALL store `unit_cost_final` as a decimal with two decimal places on each SupplierOrderItem.
5. THE System SHALL store `quantity` as an integer on each SupplierOrderItem.
6. WHEN a User removes all SupplierOrderItems from a SupplierOrder during editing, THE System SHALL return a validation error preventing the save.
7. THE System SHALL record `created_at` for each SupplierOrderItem and SHALL NOT update it after creation.

---

### Requirement 3: Supplier Order Status Management

**User Story:** As a User, I want to create and manage my own set of order statuses, so that I can reflect the real-world lifecycle of my supplier orders.

#### Acceptance Criteria

1. THE System SHALL provide create, edit, reorder, and delete operations for SupplierOrderStatuses via the StatusDrawer component.
2. THE System SHALL allow a SupplierOrderStatus to have a `name` (required), a `description` (optional), and a `sort_order` (optional integer).
3. WHEN a User creates a SupplierOrderStatus with a duplicate `name`, THE System SHALL return a validation error.
4. WHEN a User deletes a SupplierOrderStatus that is assigned to one or more SupplierOrders, THE System SHALL prevent deletion and return an error message.
5. THE StatusDrawer SHALL be a reusable component parameterised by entity type so that it can manage statuses for other entities in the project without modification.
6. WHEN a User reorders SupplierOrderStatuses, THE System SHALL persist the updated `sort_order` values for all affected statuses.

---

### Requirement 4: Tracking Number Persistence

**User Story:** As a User, I want to record tracking numbers for a supplier order and have all of them persist, so that I can trace the full shipping history even when tracking numbers change.

#### Acceptance Criteria

1. THE System SHALL allow a User to add one or more SupplierOrderTrackings to a SupplierOrder at any time.
2. THE System SHALL persist all SupplierOrderTracking entries; existing entries SHALL NOT be deleted when a new tracking number is added.
3. WHEN a User adds a SupplierOrderTracking, THE System SHALL require a non-empty tracking value.
4. THE System SHALL display all SupplierOrderTrackings for a given SupplierOrder in chronological order by creation time.

---

### Requirement 5: Order Splitting

**User Story:** As a User, I want to split a supplier order by selecting which items move to a new order, so that I can handle partial shipments while the original order retains its order number.

#### Acceptance Criteria

1. WHEN a User initiates an OrderSplit, THE System SHALL allow the User to select which SupplierOrderItems are moved into the new SupplierOrder.
2. WHEN a User initiates an OrderSplit, THE System SHALL retain the original SupplierOrder with its existing `order_number` and the remaining (unselected) SupplierOrderItems.
3. WHEN a User initiates an OrderSplit, THE System SHALL require the User to manually enter a new `order_number` for the split-off SupplierOrder.
4. WHEN a User initiates an OrderSplit, THE System SHALL copy the `supplier_id` and `status_id` from the original SupplierOrder to the new SupplierOrder.
5. WHEN a User initiates an OrderSplit, THE System SHALL allow the User to independently set `ordered_at`, `shipped_at`, `arrived_at`, and tracking numbers on the new SupplierOrder.
6. WHEN a User initiates an OrderSplit, THE System SHALL require that both the original and the new SupplierOrder each contain at least one SupplierOrderItem.

---

### Requirement 6: Payment Schedule

**User Story:** As a User, I want to record one or more payments against a supplier order, so that I can track partial payment arrangements such as 50% upfront and 50% on arrival.

#### Acceptance Criteria

1. THE System SHALL allow a SupplierOrder to have one or more Payments.
2. THE System SHALL persist Payments in a dedicated `supplier_order_payments` database table with a foreign key to the SupplierOrder.
3. WHEN a User adds a Payment, THE System SHALL require an `amount` greater than zero and a `paid_at` date.
4. THE System SHALL store `amount` as a decimal with two decimal places on each Payment.
5. THE System SHALL display all Payments for a SupplierOrder sorted by `paid_at` ascending.
6. WHEN a User deletes a Payment, THE System SHALL remove only that Payment without affecting other Payments or the SupplierOrder.

---

### Requirement 7: Supplier Order Index and Filtering

**User Story:** As a User, I want to view all supplier orders in a table with key information, so that I can quickly find and manage orders.

#### Acceptance Criteria

1. THE System SHALL display SupplierOrders in a paginated or fully-loaded table showing at minimum: order number, supplier name, status, total item count, and `ordered_at`.
2. THE System SHALL load SupplierOrders with their associated supplier, status, and item count in a single query to avoid N+1 problems.
3. WHEN a SupplierOrder has a counterpart sharing the same original `order_number` (i.e. a split order), THE System SHALL visually indicate the relationship in the index view.
4. THE System SHALL allow a User to update the `status_id` of a SupplierOrder directly from the index table via an inline select control, without navigating to the edit form.
