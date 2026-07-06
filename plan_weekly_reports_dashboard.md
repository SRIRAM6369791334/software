# Plan: Weekly Billing, Reports & Dashboard Day-Load Integration

## Overview
Add day-load data (vendor bird supply / dealer bird deliveries) to three areas that currently ignore it.

---

## 1. Weekly Billing (`/billing/weekly`)

### Files to modify:
- `app/Http/Controllers/Billing/WeeklyBillingController.php`
- `app/Services/WeeklyBillingService.php`
- `resources/views/billing/weekly/index.blade.php`

### Changes:

#### a) `WeeklyBillingService::calculateWeeklyTotals()`
Add day-load entry totals alongside DealerPurchase totals:
- Query `DayLoadEntry` for the dealer + period where `status = 'Active'`
- Compute `totalDayLoadBoxes`, `totalDayLoadBirdWeight`, `totalDayLoadFarmWeight`
- Return these in the totals array

#### b) `WeeklyBillingController::calculatePreview()`
Include day-load stats in JSON response: boxes, bird_weight, farm_weight

#### c) `resources/views/billing/weekly/index.blade.php` — Tab 3 (Purchase Log)
- Currently shows DealerPurchase records
- Add a "Day-Load Deliveries" sub-section showing day-load entries for the dealer/period with summary badges (Boxes, Bird Weight, Loss)
- Or merge both DealerPurchase + day-load into one unified log table with type badge (`Purchase` vs `Day-Load`)

#### d) Tab 4 (Generate Weekly Bill)
- Show day-load summary in the preview (boxes, bird weight alongside purchase totals)
- Day-load entries already have their own invoice flow via `dealerInvoice()`, so this tab mainly needs visibility — not full generation

---

## 2. Reports (`/reports/*`)

### Files to modify:
- `app/Services/ReportService.php`
- `resources/views/reports/sales/daily.blade.php`
- `resources/views/reports/sales/weekly.blade.php`
- `resources/views/reports/sales/monthly.blade.php`
- `resources/views/reports/purchases/daily.blade.php`
- `resources/views/reports/purchases/weekly.blade.php`
- `resources/views/reports/purchases/monthly.blade.php`
- `resources/views/reports/index.blade.php`

### Changes:

#### a) `ReportService.php`
Add day-load queries to each report method:

| Method | Add |
|--------|-----|
| `getIndexSummary()` | `totalDayLoadBoxes`, `totalDayLoadBirdWeight` (MTD) |
| `getDailySales()` | `dayLoadBirdDeliveries` (sum bird_weight to dealers, date filter) |
| `getWeeklySales()` | `dayLoadBirdDeliveries` (sum bird_weight to dealers, period filter) |
| `getMonthlySales()` | `dayLoadBirdDeliveries` (sum bird_weight to dealers, month filter) |
| `getDailyPurchases()` | `dayLoadBirdSupply` (sum bird_weight from vendors, date filter) |
| `getWeeklyPurchases()` | `dayLoadBirdSupply` (sum bird_weight from vendors, period filter) |
| `getMonthlyPurchases()` | `dayLoadBirdSupply` (sum bird_weight from vendors, month filter) |

Query pattern:
```php
$dayLoadBirdWeight = DayLoadEntry::where('status', '!=', 'Cancelled')
    ->whereHas('batch', fn($q) => $q->whereDate('billing_date', $date))
    ->sum('bird_weight');
```

#### b) Report views — Add stat cards
Add a "Day-Load Bird Supply/Delivery" stat card (with icon, color) to each report view showing the total bird weight.

#### c) Report index view
Add "Day-Load Birds" stat showing MTD bird weight

---

## 3. Dashboard (`/`)

### Files to modify:
- `app/Services/DashboardService.php`
- `resources/views/dashboard/index.blade.php`

### Changes:

#### a) `DashboardService::getStats()`
Add day-load stats:
```php
$dayLoadToday = DayLoadEntry::where('status', '!=', 'Cancelled')
    ->whereHas('batch', fn($q) => $q->whereDate('billing_date', today()))
    ->sum('bird_weight');

$dayLoadMTD = DayLoadEntry::where('status', '!=', 'Cancelled')
    ->whereHas('batch', fn($q) => $q->whereBetween('billing_date', [$startOfMonth, $endOfMonth]))
    ->sum('bird_weight');

$dayLoadBoxesMTD = DayLoadEntry::where('status', '!=', 'Cancelled')
    ->whereHas('batch', fn($q) => $q->whereBetween('billing_date', [$startOfMonth, $endOfMonth]))
    ->sum('no_of_boxes');
```

Return as `dayLoadToday`, `dayLoadMTD`, `dayLoadBoxesMTD`.

#### b) Dashboard index view
- Replace "Total Birds" stat card to also show day-load context OR
- Add a new "Day-Load Today" stat card below existing cards showing boxes + bird weight
- Show day-load MTD in the Financial Health card

---

## Implementation Order
1. **Dashboard** (simplest, standalone change)
2. **Reports** (add queries to service + update views)
3. **Weekly Billing** (integrate day-load into existing tabs)

## Verification
- `php artisan view:cache` — must pass
- Visit each modified page and verify day-load data appears
- Check that stat cards show correct numbers for the seeded data (Jun 2-8, 2025)
