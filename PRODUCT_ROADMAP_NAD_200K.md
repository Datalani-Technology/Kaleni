# /Kaleni Commerce — N$200,000 Product Roadmap

## Commercial position

The current application is a strong catering-commerce foundation: it already has a storefront, product search, cart, guest checkout, WhatsApp ordering, product recommendations, order and product administration, inventory movements, customers, expenses, reports, analytics, enquiries, promotions, gallery management, user management, audit logging, and two-factor authentication.

To credibly sell the system for about **N$200,000**, it should be positioned as a complete florist operations platform, not only as a website. The price is easiest to defend when it includes production integrations, workflow automation, migration, training, documentation, and post-launch support.

## Priority 0 — required for a production-grade sale

| Capability | What to add | Commercial value | Definition of done |
|---|---|---|---|
| Production payments | Complete DPO card/mobile-money activation, callbacks, idempotency, payment attempts, reconciliation, refunds, and failure recovery | Converts the application from an order form into real e-commerce | A real payment can be created, verified, reconciled, refunded, and audited without manual database work |
| Delivery operations | Delivery zones, distance/zone fees, delivery dates, time slots, cut-off times, blackout dates, driver assignment, dispatch status, and proof of delivery | Solves the daily operational problem behind every order | Staff can quote a fee, schedule a slot, dispatch an order, and record delivery completion |
| Order workflow | Configurable statuses, visual timeline, cancellation/refund flow, internal tasks, printable picking card, and payment/delivery state separation | Reduces errors and makes responsibility clear | Every order has a complete, timestamped operational history |
| Customer notifications | Branded email plus WhatsApp/SMS templates for confirmation, payment, preparation, dispatch, delivery, and cancellation | Reduces “where is my order?” support and improves trust | Notifications are queued, logged, retryable, and opt-out aware |
| Gift personalisation | Recipient details separate from buyer, gift message, anonymous sender option, occasion, delivery instructions, add-ons, and preferred arrangement notes | Raises average order value and fits the florist buying journey | Personalisation appears on checkout, order view, invoice/picking card, and customer confirmation |
| Inventory and costing | Pack recipes/BOM, stem and packaging stock, supplier records, purchase orders, receiving, wastage, stocktake, cost price, and gross margin | Turns simple stock counts into real florist inventory control | Selling or producing an arrangement consumes its configured components and reports actual margin |
| Reliability and recovery | Automated backups with restore drills, queues, scheduler monitoring, error tracking, health checks, deployment runbook, and staging environment | Makes the system supportable after handover | A documented restore and rollback can be demonstrated, and failed background jobs are visible |

## Priority 1 — features that strongly justify the premium

| Capability | Recommended scope | Why it matters |
|---|---|---|
| Florist POS | Walk-in sale, cash/card payment, receipt, customer lookup, discount approval, cash-up, and shared inventory | Combines online and physical sales in one platform |
| Customer CRM | Customer accounts, address book, order history, notes, consent, segments, lifetime value, and one-click reorder | Builds repeat business instead of treating each purchase as new |
| Occasion reminders | Birthdays, anniversaries, scheduled reminders, saved recipients, and pre-filled reorder links | A natural recurring-revenue feature for a florist |
| Promotions engine | Coupon rules, scheduled campaigns, bundles, gift cards, minimum spend, usage limits, and attribution | Lets staff run campaigns without developer support |
| Loyalty and referrals | Points or store credit, tiers, referral codes, expiry rules, and liability reporting | Encourages repeat orders and measurable referrals |
| Corporate accounts | Company profiles, multiple contacts, quotation and approval flow, purchase-order numbers, credit limits, statements, and VAT-ready invoices | Opens higher-value B2B and event-planning sales |
| Profit analytics | Revenue, cost of goods, delivery income/cost, waste, gross margin, customer lifetime value, cohort retention, and campaign source | Gives owners decisions, not only counts |
| Role-based access | Granular permissions for owner, manager, florist, cashier, driver, marketer, and accountant | Supports a real team while limiting financial and security exposure |

## Priority 2 — scale and productisation

- Multi-branch stock, pricing, fulfilment, cash-up, and branch performance.
- White-label configuration for logo, colours, domain, currency, tax, templates, and legal content.
- Multi-tenant architecture if the product will be sold to several florists as SaaS.
- REST/webhook API for accounting, courier, CRM, and marketplace integrations.
- Progressive Web App for staff picking, dispatch, stocktake, and delivery proof.
- Import wizard for customers, products, stock, and past orders with validation and rollback.
- Subscription/licensing, onboarding checklist, feature flags, and tenant-level billing if sold repeatedly.
- English plus additional locale support with configurable currency, tax, timezone, and date formats.

## Suggested N$200,000 scope allocation

This is a commercial packaging guide, not a fixed quotation.

| Workstream | Indicative allocation |
|---|---:|
| Architecture, security, backups, deployment, and observability | N$25,000 |
| Production payments, reconciliation, refunds, and checkout | N$30,000 |
| Delivery scheduling, dispatch, tracking, and notifications | N$35,000 |
| Inventory recipes, procurement, costing, and POS | N$40,000 |
| CRM, reminders, promotions, loyalty, and personalisation | N$25,000 |
| Corporate accounts, profitability reporting, and permissions | N$20,000 |
| QA automation, data migration, documentation, training, and launch support | N$25,000 |
| **Total target package** | **N$200,000** |

## Recommended delivery sequence

1. **Weeks 1–2 — specification and hardening:** workflow mapping, acceptance criteria, staging, backups, monitoring, permission model, and test plan.
2. **Weeks 3–4 — commerce:** production payment integration, payment ledger, refunds, improved checkout, and personalisation.
3. **Weeks 5–6 — fulfilment:** zones, fees, slots, order timeline, dispatch, delivery proof, and automated notifications.
4. **Weeks 7–8 — operations:** component inventory, recipes, suppliers, purchasing, wastage, costing, and stocktake.
5. **Weeks 9–10 — growth:** customer accounts, saved recipients, reminders, promotions, gift cards/store credit, and corporate accounts.
6. **Weeks 11–12 — productisation:** dashboards, migration, regression/security testing, staff training, documentation, production launch, and warranty handover.

## Acceptance criteria for the premium edition

- No core sales workflow requires editing the database or source code.
- Payment, order, inventory, delivery, notification, refund, and audit histories agree with each other.
- Duplicate payment callbacks and repeated form submissions cannot create duplicate charges or stock movements.
- Financial reports separate revenue, tax, discounts, refunds, cost of goods, delivery income, expenses, and gross profit.
- Permissions are tested for every staff role; 2FA and audit logging cover sensitive actions.
- Backup restore, deployment rollback, queue failure, and payment failure scenarios are demonstrated.
- Critical storefront, checkout, payment callback, order, stock, and permission paths have automated tests.
- Staff receive an administrator guide, operational runbook, training session, and defined post-launch warranty period.

## Best sales narrative

Sell the product as **“one platform from pack to delivery”**: online shop, WhatsApp-assisted sales, payments, customer history, florist production, inventory, delivery, POS, reporting, and secure administration. A polished storefront helps win the demonstration; the operational depth above is what supports the N$200,000 business case.
