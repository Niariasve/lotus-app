# Requirements Document

## Introduction

This feature implements supplier order management for the product sourcing and sales optimization system. A `SupplierOrder` tracks a confirmed purchase from a supplier. Orders contain line items (`SupplierOrderItem`) that are initially added to a flat, ungrouped list. Users can then select items from that ungrouped list and create a named `SupplierOrderItemGroup` for them. Once items are grouped, they are removed from the ungrouped list and appear instead under their group's own item list. Items can be moved between groups or back to the ungrouped list at any time. Orders progress through a configurable set of statuses (e.g. Draft, Confirmed, Shipped, Received, Cancelled) that are managed independently. Supplier orders are independent from `SupplierProductOffer` records.

---

## Glossary

- **SupplierOrder**: A purchase order placed with a specific supplier, containing one or more order items.
- **SupplierOrderItem**: A line item within a `SupplierOrder` representing a specific product, its confirmed quantity, and its confirmed unit cost.
- **SupplierOrderItemGroup**: A named grouping of one or more `SupplierOrderItem` records within the same `SupplierOrder`. Groups are created by the user by selecting items from the ungrouped list.
- **Ungrouped item**: A `SupplierOrderItem` that has no `group_id` assigned. It appears in the ungrouped items list on the order detail view.
- **Grouped item**: A `SupplierOrderItem` that has a `group_id` assigned. It appears only within its group's item list, not in the ungrouped list.
- **SupplierOrderStatus**: A named status label that describes the current lifecycle stage of a `SupplierOrder` (e.g. Draft, Confirmed, Shipped, Received, Cancelled).
- **Supplier**: An existing entity representing a product provider.
- **System**: The Laravel/Inertia application described in this document.

---

## Requirements

### Requirement 1: Supplier Order Status Management

**User Story:** As an administrator, I want to manage supplier order statuses, so that I can define and maintain the lifecycle stages that orders move through.

#### Acceptance Criteria

1. THE System SHALL provide a CRUD interface for `SupplierOrderStatus` records with `name` (required, unique, max 150 characters) and `description` (optional) fields.
2. WHEN a `SupplierOrderStatus` is created with a name that already exists, THE System SHALL return a validation error indicating the name must be unique.
3. WHEN a `SupplierOrderStatus` that is assigned to one or more `SupplierOrder` records is deleted, THE System SHALL set the `status_id` of those orders to null rather than deleting the orders.
4. THE System SHALL display all `SupplierOrderStatus` records in a paginated, sortable list showing name and description.

---

### Requirement 2: Supplier Order Creation

**User Story:** As an administrator, I want to create a supplier order, so that I can record a confirmed purchase from a supplier.

#### Acceptance Criteria

1. THE System SHALL provide a form to create a `SupplierOrder` with the following fields: `supplier_id` (required), `status_id` (optional), and `tracking` (optional, max 1000 characters).
2. WHEN a `SupplierOrder` is stored, THE System SHALL associate it with an existing `Supplier`.
3. WHEN a `SupplierOrder` is stored with an invalid or non-existent `supplier_id`, THE System SHALL return a validation error.
4. WHEN a `SupplierOrder` is stored with an invalid or non-existent `status_id`, THE System SHALL return a validation error.

---

### Requirement 3: Supplier Order Item Management

**User Story:** As an administrator, I want to add and manage line items on a supplier order, so that I can record the exact products, quantities, and confirmed costs for each order.

#### Acceptance Criteria

1. THE System SHALL allow one or more `SupplierOrderItem` records to be associated with a `SupplierOrder`, each with: `product_id` (required), `quantity` (required, integer ≥ 1), and `unit_cost` (required, decimal ≥ 0).
2. WHEN a `SupplierOrderItem` is created with a `product_id` that does not exist, THE System SHALL return a validation error.
3. WHEN a `SupplierOrderItem` is created with a `quantity` less than 1, THE System SHALL return a validation error.
4. WHEN a `SupplierOrderItem` is created with a `unit_cost` less than 0, THE System SHALL return a validation error.
5. THE System SHALL allow `SupplierOrderItem` records to be added, updated, and removed from an existing `SupplierOrder`.
6. WHEN a `SupplierOrder` is deleted, THE System SHALL delete all associated `SupplierOrderItem` records and `SupplierOrderItemGroup` records.
7. A newly created `SupplierOrderItem` SHALL have no group assigned (`group_id` is null) and SHALL appear in the unified item list as an ungrouped item.

---

### Requirement 4: Supplier Order Item Grouping

**User Story:** As an administrator, I want to select items from the ungrouped items list and group them together, so that I can organize related items within an order.

#### Acceptance Criteria

1. THE System SHALL allow the user to select one or more `SupplierOrderItem` records from the ungrouped items list and create a new `SupplierOrderItemGroup` with a `name` (required, max 150 characters) for those items.
2. WHEN a group is created, THE System SHALL assign the selected items to that group by setting their `group_id`, and those items SHALL no longer appear in the ungrouped items list.
3. WHEN a group is created, THE System SHALL require at least one item to be selected.
4. THE System SHALL display ungrouped items and grouped items in separate lists on the order detail view. Ungrouped items appear in the ungrouped items list. Each group has its own item list showing only the items belonging to that group.
5. THE System SHALL allow the user to move a `SupplierOrderItem` from one group to another by updating its `group_id`. The item SHALL disappear from the source group's list and appear in the destination group's list.
6. THE System SHALL allow the user to move a `SupplierOrderItem` back to the ungrouped list by setting its `group_id` to null. The item SHALL disappear from the group's list and reappear in the ungrouped items list.
7. WHEN a `SupplierOrderItemGroup` is deleted, THE System SHALL set the `group_id` of all its items to null, returning them to the ungrouped items list rather than deleting them.
8. THE System SHALL allow a `SupplierOrderItemGroup` to be renamed.
9. WHEN a `SupplierOrderItemGroup` name is updated with an empty or missing value, THE System SHALL return a validation error.

---

### Requirement 5: Supplier Order Listing and Detail

**User Story:** As an administrator, I want to view all supplier orders and their details, so that I can monitor purchasing activity.

#### Acceptance Criteria

1. THE System SHALL display a paginated, sortable list of `SupplierOrder` records showing: supplier name, current status, tracking number, item count, and creation date.
2. THE System SHALL provide a detail view for each `SupplierOrder` that shows two distinct sections: an ungrouped items list (items with no group) and a groups section (each group with its own item list). Each item row shows product name, quantity, unit cost, and computed line total (`quantity × unit_cost`).
3. THE System SHALL display the computed order total (sum of all line totals across all items, grouped and ungrouped) on the `SupplierOrder` detail view.
4. WHEN a `SupplierOrder` has no `status_id`, THE System SHALL display the status as blank or "—" rather than an error.
5. THE System SHALL display all `SupplierOrderItemGroup` records for an order in the groups section, each with its own item list and management actions (rename, delete).

---

### Requirement 6: Supplier Order Editing

**User Story:** As an administrator, I want to edit a supplier order, so that I can update tracking information, status, or correct order details.

#### Acceptance Criteria

1. THE System SHALL provide an edit form for a `SupplierOrder` pre-populated with its current `supplier_id`, `status_id`, and `tracking` values.
2. WHEN a `SupplierOrder` is updated with valid data, THE System SHALL persist the changes and redirect to the order list with a success message.
3. WHEN a `SupplierOrder` is updated with invalid data, THE System SHALL return validation errors without persisting changes.

---

### Requirement 7: Supplier Order Deletion

**User Story:** As an administrator, I want to delete a supplier order, so that I can remove erroneous or cancelled orders from the system.

#### Acceptance Criteria

1. WHEN a `SupplierOrder` is deleted, THE System SHALL remove the order, all its `SupplierOrderItem` records, and all its `SupplierOrderItemGroup` records from the database.
2. WHEN a `SupplierOrder` is successfully deleted, THE System SHALL redirect to the order list with a success message.

---

### Requirement 8: Authentication and Authorization

**User Story:** As a system owner, I want all supplier order and status endpoints to require authentication, so that unauthenticated users cannot access or modify order data.

#### Acceptance Criteria

1. WHEN an unauthenticated user attempts to access any `SupplierOrder`, `SupplierOrderStatus`, or `SupplierOrderItemGroup` route, THE System SHALL redirect the user to the login page.
2. THE System SHALL apply the `auth` middleware to all `SupplierOrder`, `SupplierOrderStatus`, and `SupplierOrderItemGroup` routes.
