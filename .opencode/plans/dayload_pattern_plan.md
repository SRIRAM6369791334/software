# Plan: Apply Day-Load Flow Pattern to Sales, Purchases & Invoices

## Reference — Day-Load Billing Flow ("Perfect" Pattern)
The day-load billing page (`/billing/day-load`) has these features:

1. **Date-focused** — one batch/day at a time, date picker to switch
2. **Summary stat cards** — Total Boxes, Bird Weight, Farm Weight, Total Weight
3. **Inline create form** — directly on the page
4. **Clean data table** — one row per entry with vendor, dealer, weights, rates, status
5. **Inline modal editing** — Edit button → modal opens with all fields → Save
6. **Batch operations** — Set Farm Weight (proportional), Adjust All (bulk edit)
7. **Transfer** — Move boxes between entries
8. **Search** — filter by vendor or dealer

---

## Key Pattern Details to Replicate

### A. Date-Focused View
```php
// Controller pattern:
$date = $request->input('date', today()->format('Y-m-d'));
$entries = Model::whereDate('date_column', $date)->latest()->paginate(15);
```
Top stat cards show: Date, Day name, totals for the selected date.

### B. Inline Modal Editing
```blade
// View pattern:
<button x-on:click="
    $dispatch('open-modal', 'edit-modal');
    $nextTick(() => {
        editId = {{ $entry->id }};
        editField1 = {{ $entry->field1 }};
    });
">
    Edit
</button>

<x-modal name="edit-modal" title="Edit Entry" ...>
    <form :action="editFormAction" method="POST">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        {{-- Fields bound to x-model --}}
    </form>
</x-modal>
```

### C. Summary Stat Cards
```blade
<x-stat-card label="Total Amount" value="Rs {{ number_format($totalAmount, 0) }}" icon="payments" color="emerald" />
```

---

## 1. Purchases — `/purchases/entry`

### Current Flow
- Inline form for recording feed/inventory purchases
- Recent purchase logs table with search
- Already has vendor day-load supply section (added earlier)

### Target Flow (Day-Load Pattern)

#### a) `PurchaseController::index()` Add:
- `$date` param (default today)
- `$dailyStats` for selected date: total_amount, total_gst, item_count, vendor_count
- Separate pagination for daily vs all purchases

#### b) `purchases/index.blade.php` Restructure:
```
┌──────────────────────────────────────────────────────────────┐
│ [Date Picker] [Day Name]  [Total: ₹X] [GST: ₹X] [Items: N]  │
├──────────────────────────────────────────────────────────────┤
│ Inline Create Form (expandable)                              │
├──────────────────────────────────────────────────────────────┤
│ Purchase Log Table: Date | Vendor | Invoice | Items | GST |  │
│ Total | Mode | Actions [Edit] [Delete]                       │
│                                                              │
│ With inline modal editing: Edit → modal popup → save         │
├──────────────────────────────────────────────────────────────┤
│ Vendor Day-Load Supply (existing section)                    │
└──────────────────────────────────────────────────────────────┘
```

#### c) Inline Modal for Editing Purchases
Add Alpine.js `x-data` block with edit state variables. Click Edit on a purchase → modal with:
- Vendor (select)
- Invoice No (text)
- Date (date)
- Payment Mode (select)
- Items table (dynamic rows with name, qty, rate)
- GST %
- Reason field

#### d) Controller: `PurchaseController::editInline()` or reuse existing `update()`
The existing `update()` method already handles purchase updates from the edit page. For inline modal, we can use the same route but submit via AJAX or just a regular form in the modal.

Actually, looking at the existing code, there's already an `update()` method at `PUT /purchases/{purchase}`. We can use this from the modal. Just need to add the modal to the index view.

#### e) New/modified files:
| File | Change |
|------|--------|
| `app/Http/Controllers/Purchases/PurchaseController.php` | Add `$date` + `$dailyStats` to `index()` |
| `resources/views/purchases/index.blade.php` | Add date picker, summary cards, inline edit modal |

---

## 2. Sales (Daily Billing) — `/billing/daily`

### Current Flow
- Inline form for recording retail sales
- Recent counter sales table
- Stat cards (Today's Sales, Avg Ticket Size, Total Cash)
- Already has dealer day-load deliveries section (added earlier)

### Target Flow (Day-Load Pattern)

#### a) `DailyBillingController::index()` Add:
- `$date` param (default today)
- `$dailyStats` for selected date: total_sale, total_gst, cash_sales, credit_sales, item_count
- Filter bills by date when date param is present

#### b) `billing/daily/index.blade.php` Restructure:
```
┌──────────────────────────────────────────────────────────────┐
│ [Date Picker] [Day Name]  [Sale: ₹X] [GST: ₹X] [Items: N]   │
├──────────────────────────────────────────────────────────────┤
│ Inline Create Form (expandable)                             │
├──────────────────────────────────────────────────────────────┤
│ Sales Table: Invoice | Customer | Items | Qty | Amount |     │
│ Status | Actions [Edit] [Print] [PDF]                       │
│                                                              │
│ With inline modal editing: Edit → modal popup → save         │
├──────────────────────────────────────────────────────────────┤
│ Dealer Deliveries (existing section)                        │
└──────────────────────────────────────────────────────────────┘
```

#### c) Inline Modal for Editing Sales
Add Alpine.js edit state. Click Edit → modal with:
- Customer (select)
- Date (date)
- Payment Mode (select)
- Status (select: Paid/Pending/Generated)
- Items table (dynamic rows)
- GST %
- Reason field

#### d) Controller: Add `update()` method
A `DailyBillingController::update()` needs to be created for inline editing. Currently only `store()` exists.
- Route: `PUT /billing/daily/{bill}` (add to routes)
- Validates and updates bill + items
- Redirects back to daily billing index

#### e) New/modified files:
| File | Change |
|------|--------|
| `app/Http/Controllers/Billing/DailyBillingController.php` | Add `$date` + `$dailyStats` to `index()`, add `update()` method |
| `resources/views/billing/daily/index.blade.php` | Add date picker, summary cards, inline edit modal |
| `routes/web.php` | Add `PUT billing/daily/{bill}` route |

---

## 3. Invoices (Purchase Invoices) — `/purchases/invoices`

### Current Flow
- Date-grouped view combining purchase dates + day-load batch dates
- Single day view shows both day-load entries and purchase entries
- Already has good stat cards

### Target Flow (Day-Load Pattern)
This page already follows the day-load pattern quite well (we redesigned it earlier). Main changes:
- **Date list view**: Polish stat cards (add Day-Load Boxes, Bird Weight)
- **Single day view**: Ensure both tables follow day-load styling
- **Inline modal editing**: For purchase entries in single day view

### New/modified files:
| File | Change |
|------|--------|
| `resources/views/purchases/invoices.blade.php` | Polish stat cards |
| `resources/views/purchases/invoices-day.blade.php` | Add inline edit modal for purchases |

---

## Implementation Order

1. **Purchases** — date picker + summary cards + inline edit modal
2. **Invoices** — polish stat cards + inline edit for purchases in day view
3. **Sales** — date picker + summary cards + update controller method + inline edit modal

## Files to Create/Modify

### Purchases:
- `app/Http/Controllers/Purchases/PurchaseController.php`
- `resources/views/purchases/index.blade.php`

### Invoices:
- `resources/views/purchases/invoices.blade.php`
- `resources/views/purchases/invoices-day.blade.php`
- `app/Http/Controllers/Purchases/PurchaseController.php` (invoices method)

### Sales:
- `app/Http/Controllers/Billing/DailyBillingController.php`
- `resources/views/billing/daily/index.blade.php`
- `routes/web.php`

## Verification
- `php artisan view:cache` — must pass
- Each page: date filter works, summary cards show correct values
- Inline modal editing: open modal → change fields → save → page refreshes with updated data
- Test with seeded data (Jun 2-8, 2025 for day-load; purchases/sales may need their own data)
