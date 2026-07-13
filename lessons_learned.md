# Lessons Learned - Flockwise Biztrack Laravel

## [Architecture]
- All payments (Customers, Dealers, Vendors) use similar double-entry or balance-tracking architectures where payouts are recorded in a payment table and corresponding balances are adjusted.
- Cash & Bank Ledgers need to be updated (e.g., calling `app(CashBankLedgerService::class)->recalculateForDate(...)` or similar services) whenever payments are recorded or deleted.

## [UI_Bugs]
- When using modals for recording payments (like in day-load index), Alpine.js properties must be set on `$nextTick` or mapped correctly to avoid null pointer/undefined binding exceptions.
