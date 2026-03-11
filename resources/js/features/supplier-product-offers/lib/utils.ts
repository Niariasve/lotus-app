import { type RawRetailPriceParams } from "@/types";

export const toNumberOrUndefined = (value: string | number): number | undefined => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : undefined;
};

export const formatMoney = (value: number | undefined): string => {
    if (value === undefined || Number.isNaN(value)) return 'N/A';
    return Number(value).toFixed(2);
};

export const formatPercent = (value: number | undefined): string => {
    if (value === undefined || Number.isNaN(value)) return 'N/A';
    return `${(value * 100).toFixed(2)}%`;
};

export const calculateRawRetailPrice = ({
    baseCost,
    estimatedShipping,
    tax,
    productWeight,
    courierFee
}: RawRetailPriceParams): number | undefined => {
    if (
        baseCost === undefined ||
        estimatedShipping === undefined ||
        tax === undefined ||
        productWeight === undefined ||
        courierFee === undefined
    ) return undefined;


    return ((baseCost + estimatedShipping) * (1 + tax)) + (courierFee * productWeight);
}

export const calculateRetailPriceFromProfit = (
    rawRetailPrice?: number,
    profitPercentage?: number,
): number | undefined => {
    if (rawRetailPrice === undefined || profitPercentage === undefined) return undefined;

    return Number((rawRetailPrice * (1 + profitPercentage)).toFixed(2));
}

export const calculateProfitPercentageFromRetail = (
    rawRetailPrice?: number,
    retailPrice?: number,
): number | undefined => {
    if (
        rawRetailPrice === undefined ||
        retailPrice === undefined ||
        rawRetailPrice <= 0
    ) {
        return undefined;
    }

    return Number(((retailPrice / rawRetailPrice) - 1).toFixed(4));
};