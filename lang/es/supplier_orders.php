<?php

declare(strict_types=1);

return [
    'title' => 'Órdenes de Proveedor',
    'description' => 'Gestionar órdenes de compra a proveedores',
    'fields' => [
        'order_number' => 'Número de Orden',
        'supplier' => 'Proveedor',
        'status' => 'Estado',
        'tracking' => 'Seguimiento',
        'ordered_at' => 'Fecha de Orden',
        'shipped_at' => 'Fecha de Envío',
        'arrived_at' => 'Fecha de Llegada',
        'items' => 'Artículos',
        'unit_cost' => 'Costo Unitario',
        'quantity' => 'Cantidad',
        'line_total' => 'Total de Línea',
        'order_total' => 'Total de Orden',
    ],
    'no_status' => '—',
];
