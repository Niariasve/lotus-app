<?php

declare(strict_types=1);

return [
    'title' => 'Órdenes de Proveedor',
    'description' => 'Gestionar órdenes de compra a proveedores',
    'create' => [
        'title' => 'Crear Orden de Proveedor',
        'description' => 'Cargá el encabezado de la orden y sus renglones en un solo flujo.',
        'order_details' => 'Detalles de la Orden',
        'order_details_description' => 'Seleccioná el proveedor, el estado opcional y las fechas importantes de la orden.',
        'items_description' => 'Agregá al menos un renglón con su producto, cantidad y costo unitario.',
        'summary' => 'Resumen de la Orden',
        'summary_description' => 'Revisá el total acumulado antes de guardar la orden.',
        'add_item' => 'Agregar Artículo',
        'remove_item' => 'Quitar Artículo',
        'empty_status' => 'Sin estado',
        'status_description' => 'Dejá este campo vacío si la orden todavía no tiene un estado asignado.',
    ],
    'edit' => [
        'title' => 'Editar Orden de Proveedor',
        'description' => 'Ajustá los datos de la orden y sus renglones sin perder la estructura actual.',
    ],
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
