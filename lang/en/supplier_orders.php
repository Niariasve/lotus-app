<?php

declare(strict_types=1);

return [
    'title' => 'Supplier Orders',
    'description' => 'Manage supplier purchase orders',
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
];
