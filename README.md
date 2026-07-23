# ACDirect – Magento 2 storefront work (showcase)

Selected custom code from **ACDirect** (HVAC e-commerce, Magento 2.4.5, Porto-based theme), shared to show the product-page, pricing and checkout work I led on the store before it moved to Shopify.

## What's in here

Only the parts of the codebase my work touched:

| Path | What it is |
|---|---|
| `app/code/Thecommerceshop/Pdpnewdesign` | Product-page (PDP) module: bundle option templates, sticky add-to-cart, cart drawer, layout |
| `app/code/Thecommerceshop/Map` | MAP (Minimum Advertised Price) module: hidden-price display, discount lookup, cart-rule sync cron |
| `app/design/frontend/...` | Theme overrides for category cards, widget grids and the one-page checkout |

Vendor/extension code, the commercial theme, config, credentials and all data are intentionally left out.

## How to read it

- **`main` (initial commit)** – the code *as it was before my changes*. It was written by the store's previous development agency and is included only as the baseline.
- **PR #1 – `feature/pdp-redesign`** – the bundle PDP redesign.
- **PR #2 – `feature/pricing-checkout-followups`** – pricing and checkout follow-ups (stacked on PR #1).

Each PR keeps the original commits and dates from the production repository, re-applied with only their own diffs.

## Context

- Bundles are "system builders" (outdoor unit + 1–5 indoor units + accessories + warranty). Options are grouped automatically into *Included*, *Complete Your System* and *Warranty* based on each option's shape.
- Some manufacturers enforce MAP, so for those brands the real price is revealed in the cart. Other brands (Goodman, Daikin, MrCool, Lennox) show it openly as "Today's Special Price".
- The displayed discount comes from auto-apply cart price rules, flattened per product by a cron into a `map_discount` lookup table.

## How it was built

I scoped, reviewed, tested and deployed this work to production myself. I wrote it with an AI coding assistant (Claude Code) as a pair programmer.
