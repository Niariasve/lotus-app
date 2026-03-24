import { type Product, type Supplier, type SupplierOrderStatus } from '@/types';

export interface SupplierOrderItem {
    id: number,
    supplier_order_id: number,
    product_id: number,
    quantity: number,
    unit_cost: number | string,
    created_at: string,
    updated_at: string,
    product?: Product | null,
}

export interface SupplierOrder {
    id: number,
    order_number: string,
    supplier_id: number,
    status_id: number | null,
    tracking: string | null,
    ordered_at: string | null,
    shipped_at: string | null,
    arrived_at: string,
    created_at: string,
    updated_at: string,
    supplier?: Supplier | null,
    status?: SupplierOrderStatus | null,
    items?: SupplierOrderItem[],
    items_count?: number,
}
