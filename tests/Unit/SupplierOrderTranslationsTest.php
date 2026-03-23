<?php

uses(Tests\TestCase::class);

test('supplier order translations are loaded from locale files', function () {
    app('translator')->setLocale('en');

    expect(__('supplier_orders.title'))->toBe('Supplier Orders')
        ->and(__('supplier_orders.fields.order_number'))->toBe('Order Number')
        ->and(__('supplier_order_statuses.description'))
        ->toBe('Manage order lifecycle statuses');

    app('translator')->setLocale('es');

    expect(__('supplier_orders.title'))->toBe('Órdenes de Proveedor')
        ->and(__('supplier_orders.fields.order_number'))->toBe('Número de Orden')
        ->and(__('supplier_order_statuses.description'))
        ->toBe('Gestionar estados del ciclo de vida de órdenes');
});
