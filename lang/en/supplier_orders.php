<?php

declare(strict_types=1);

return [
    'title' => 'Supplier Orders',
    'description' => 'Manage supplier purchase orders',
    'create' => [
        'title' => 'Create Supplier Order',
        'description' => 'Capture the order header and its line items in a single flow.',
        'order_details' => 'Order Details',
        'order_details_description' => 'Select the supplier, optional status, and the important order dates.',
        'items_description' => 'Add at least one line item with its product, quantity, and unit cost.',
        'summary' => 'Order Summary',
        'summary_description' => 'Review the running total before saving the order.',
        'add_item' => 'Add Item',
        'remove_item' => 'Remove Item',
        'empty_status' => 'No status',
        'status_description' => 'Leave this blank if the order does not have a lifecycle status yet.',
    ],
    'edit' => [
        'title' => 'Edit Supplier Order',
        'description' => 'Adjust order details and line items without losing the current structure.',
    ],
    'show' => [
        'title' => 'Supplier Order Details',
        'description' => 'Review the supplier order timeline, status, and line items in one place.',
        'order_details_description' => 'Snapshot of the supplier, lifecycle state, tracking notes, and key dates.',
        'items_description' => 'All products included in this supplier order with their computed line totals.',
        'summary' => 'Order Snapshot',
        'summary_description' => 'Quick totals and status context for this order.',
    ],
    'fields' => [
        'order_number' => 'Order Number',
        'supplier' => 'Supplier',
        'status' => 'Status',
        'tracking' => 'Tracking',
        'ordered_at' => 'Ordered At',
        'shipped_at' => 'Shipped At',
        'arrived_at' => 'Arrived At',
        'items' => 'Items',
        'unit_cost' => 'Unit Cost',
        'quantity' => 'Quantity',
        'line_total' => 'Line Total',
        'order_total' => 'Order Total',
    ],
    'no_status' => '—',
    'no_tracking' => 'No tracking notes provided.',
];
