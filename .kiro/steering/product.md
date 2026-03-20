# Product Overview

This is a **product sourcing and sales optimization system** for a business that sells physical products. The core purpose is: track multiple suppliers per product, calculate real costs, and determine the most profitable sourcing strategy — without manual calculations.

## The Problem

A single product can have multiple suppliers, each with different base costs, tax rates, shipping rates, and availability. The system answers: "Which supplier gives me the best real cost right now, and what margin will I make?"

## Core Entity Relationship

```
Product ↔ SupplierProductOffer ↔ Supplier
```

- `Product` — what is sold (SKU, name, weight, dimensions, etc.)
- `Supplier` — who provides it (with default tax/shipping policies as percentages)
- `SupplierProductOffer` — the offer from a specific supplier for a specific product (base cost, tax %, shipping %, availability, last checked date)

## Cost Calculation

Real cost is computed (not stored directly):

```
real_cost = base_cost + (base_cost * tax_percentage) + (base_cost * shipping_percentage)
```

This normalizes costs across suppliers for fair comparison.

## Key Features

1. **Product catalog** — central entity; stores SKU, brand, line, weight estimates, release date
2. **Supplier management** — stores supplier info and default tax/shipping policies
3. **Supplier product offers** — multiple offers per product; system filters available ones and calculates real cost to identify the best option
4. **Customer management** — tracks customers with contact info and linked contact platforms
5. **Pre-orders / reservations** — customers can reserve products before stock is confirmed; supports margin estimation before committing to a purchase
6. **Supplier orders** (planned) — once a supplier is chosen, a `supplier_order` is created with `supplier_order_items` that convert estimated costs into real confirmed data

## What Is and Isn't Implemented Yet

Currently implemented:
- Product, Supplier, SupplierProductOffer, Customer, ContactPlatform CRUD
- Offer availability toggling
- Auth, 2FA, locale, theming

Planned / not yet implemented:
- Sales & reservations layer
- Supplier order creation and tracking
- Automated best-offer selection logic
- Margin reporting

## One-Line Summary

> A system that models multiple supplier offers per product, uses cost calculations to determine the best sourcing option, and supports pre-orders and real order tracking to maintain consistent margins.
