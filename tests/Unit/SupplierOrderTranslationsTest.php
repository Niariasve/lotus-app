<?php

uses(Tests\TestCase::class);

test('supplier order translations are loaded from locale files', function () {
    app('translator')->setLocale('en');

    expect(__('supplier_orders.title'))->toBe('Supplier Orders')
        ->and(__('supplier_orders.fields.order_number'))->toBe('Order Number')
        ->and(__('supplier_orders.create.title'))->toBe('Create Supplier Order')
        ->and(__('supplier_orders.edit.title'))->toBe('Edit Supplier Order')
        ->and(__('supplier_orders.show.title'))->toBe('Supplier Order Details')
        ->and(__('supplier_order_statuses.description'))
        ->toBe('Manage order lifecycle statuses');

    app('translator')->setLocale('es');

    expect(__('supplier_orders.title'))->toBe('Órdenes de Proveedor')
        ->and(__('supplier_orders.fields.order_number'))->toBe('Número de Orden')
        ->and(__('supplier_orders.create.title'))->toBe('Crear Orden de Proveedor')
        ->and(__('supplier_orders.edit.title'))->toBe('Editar Orden de Proveedor')
        ->and(__('supplier_orders.show.title'))->toBe('Detalle de la Orden de Proveedor')
        ->and(__('supplier_order_statuses.description'))
        ->toBe('Gestionar estados del ciclo de vida de órdenes');
});
