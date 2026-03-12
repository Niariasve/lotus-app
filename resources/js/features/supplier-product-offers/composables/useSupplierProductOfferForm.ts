import { computed, ref, watch } from "vue"
import SupplierProductOfferController from "@/actions/App/Http/Controllers/SupplierProductOfferController"
import { formatDateTime, trimDecimal } from "@/lib/utils"
import { type Product, type Supplier, type SupplierProductOffer } from "@/types"
import { DEFAULT_COURIER_FEE } from "../constants"
import { calculateProfitPercentageFromRetail, calculateRawRetailPrice, calculateRetailPriceFromProfit, toNumberOrUndefined } from "../lib/utils"

type Props = {
    supplierProductOffer?: SupplierProductOffer,
    suppliers: Supplier[],
    products: Product[],
}

export const useSupplierProductOfferForm = (props: Props) => {
    const controller = () => {
        if (!props.supplierProductOffer) return SupplierProductOfferController.store.form();
        return SupplierProductOfferController.update.form(props.supplierProductOffer!.id);
    }

    const supplierId = ref<number | null>(
        props.supplierProductOffer ? props.supplierProductOffer.supplier_id : null
    );

    const productId = ref<number | null>(
        props.supplierProductOffer ? props.supplierProductOffer.product_id : null
    );

    const selectedSupplier = computed(() =>
        props.suppliers.find((s) => s.id === supplierId.value)
    );

    const selectedProduct = computed(() =>
        props.products.find((p) => p.id === productId.value)
    );

    const estimatedShipping = ref<number | undefined>(undefined);
    const tax = ref<number | undefined>(undefined);
    const productWeight = ref<number | undefined>(undefined);

    const baseCost = ref<number | undefined>(
        props.supplierProductOffer ? Number(props.supplierProductOffer.base_cost) : undefined
    );

    const courierFee = ref<number | undefined>(DEFAULT_COURIER_FEE);

    const profitPercentage = ref<number | undefined>(
        props.supplierProductOffer ? Number(props.supplierProductOffer.profit_percentage) : undefined
    );

    const retailPrice = ref<number | undefined>(
        props.supplierProductOffer ? Number(props.supplierProductOffer.retail_price) : undefined
    );

    const pricingSource = ref<'retail' | 'profit'>('retail');

    const url = ref<string | undefined>(
        props.supplierProductOffer ? props.supplierProductOffer.url : undefined
    );

    const isAvailable = ref<string>(
        props.supplierProductOffer ? (props.supplierProductOffer.is_available ? '1' : '0') : '1'
    );

    const selectedCurrency = computed(() => selectedSupplier.value?.currency ?? 'USD');

    const currentLastCheckedAt = computed(() =>
        formatDateTime(
            props.supplierProductOffer?.last_checked_at,
            undefined,
            'en-US',
            'Will be set when created'
        )
    );

    const rawRetailPrice = computed(() =>
        calculateRawRetailPrice({
            baseCost: baseCost.value,
            estimatedShipping: estimatedShipping.value,
            tax: tax.value,
            productWeight: productWeight.value,
            courierFee: courierFee.value,
        })
    );

    const handleProfitInput = (value: string | number) => {
        pricingSource.value = 'profit';
        profitPercentage.value = toNumberOrUndefined(value);
    }

    const handleRetailInput = (value: string | number) => {
        pricingSource.value = 'retail';
        retailPrice.value = toNumberOrUndefined(value);
    }

    watch(selectedSupplier, (supplier) => {
        if (!supplier) return;

        estimatedShipping.value = Number(supplier.estimated_shipping);
        tax.value = trimDecimal(supplier.tax_policy) as number;
    }, { immediate: true });

    watch(selectedProduct, (product) => {
        if (!product) return;

        const weightValue = (product.weight_real ? product.weight_real : product.weight_est) ?? 0;
        productWeight.value = trimDecimal(weightValue) as number;
    }, { immediate: true });

    watch([rawRetailPrice, profitPercentage], ([raw, margin]) => {
        if (pricingSource.value !== 'profit') return;
        retailPrice.value = calculateRetailPriceFromProfit(raw, margin);
    }, { immediate: true });

    watch([rawRetailPrice, retailPrice], ([raw, retail]) => {
        if (pricingSource.value !== 'retail') return;
        profitPercentage.value = calculateProfitPercentageFromRetail(raw, retail);
    }, { immediate: true });

    return {
        controller,
        supplierId,
        productId,
        selectedSupplier,
        selectedProduct,
        estimatedShipping,
        tax,
        productWeight,
        baseCost,
        courierFee,
        profitPercentage,
        retailPrice,
        pricingSource,
        url,
        isAvailable,
        selectedCurrency,
        currentLastCheckedAt,
        rawRetailPrice,
        handleProfitInput,
        handleRetailInput,
    };
}
