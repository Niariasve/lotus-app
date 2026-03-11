import { type Product, type Supplier } from "@/types";

export interface SupplierProductOffer {
    id: number,
    supplier_id: number,
    product_id: number,
    priority: number | string,
    base_cost: number | string,
    retail_price: number | string,
    profit_percentage: number,
    is_available: boolean,
    last_checked_at: string | null,
    supplier?: Supplier | null,
    product?: Product | null,
}

export type RawRetailPriceParams = {
    baseCost?: number;
    estimatedShipping?: number,
    tax?: number,
    productWeight?: number,
    courierFee?: number,
}