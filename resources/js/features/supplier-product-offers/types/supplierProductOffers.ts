export interface SupplierProductOffer {
    id: number,
    supplier_id: number,
    product_id: number,
    base_cost: number | string,
    currency: string,
    estimated_tax: number | string,
    estimated_shipping: number | string,
    other_fees: number | string,
    is_available: boolean,
    last_checked_at: string | null,
    supplier?: {
        id: number,
        name: string,
    } | null,
    product?: {
        id: number,
        name: string,
        sku?: string | null,
    } | null,
}
