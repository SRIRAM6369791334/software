<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Vendor;
use App\Models\Purchase;
use App\Models\WeeklyBill;
use App\Models\DailyBill;
use App\Models\CustomerPayment;
use App\Models\DealerPayment;
use App\Models\Expense;
use App\Models\Emi;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Customers ────────────────────────────────────────────────────────
        $customers = [
            ['name' => 'Adarsh Poultry Mart', 'phone' => '9876543210', 'address' => 'Main Bazaar, Coimbatore', 'route' => 'City Route A', 'type' => 'Wholesale', 'balance' => 45000.00],
            ['name' => 'Best Eggs Center', 'phone' => '9876543211', 'address' => 'North Gate, Salem', 'route' => 'Salem Express', 'type' => 'Retail', 'balance' => 12500.00],
            ['name' => 'Cauvery Chicken', 'phone' => '9876543212', 'address' => 'River Side, Erode', 'route' => 'Erode Belt', 'type' => 'Wholesale', 'balance' => 89000.00],
            ['name' => 'Deepam Broilers', 'phone' => '9876543213', 'address' => 'South St, Madurai', 'route' => 'South Gate', 'type' => 'Retail', 'balance' => 5400.00],
            ['name' => 'Elite Poultry Farm', 'phone' => '9876543214', 'address' => 'Hill View, Ooty', 'route' => 'Hills Route', 'type' => 'Wholesale', 'balance' => 120000.00],
        ];

        foreach ($customers as $c) {
            Customer::create($c);
        }

        // ── 2. Dealers ──────────────────────────────────────────────────────────
        $dealers = [
            ['firm_name' => 'Annai Feeds Pvt Ltd', 'contact_person' => 'Ravi Kumar', 'phone' => '9000011111', 'location' => 'Namakkal', 'route' => 'Feed Route 1', 'pending_amount' => 500000.00],
            ['firm_name' => 'Bharat Hatcheries', 'contact_person' => 'Sunil Verma', 'phone' => '9000011112', 'location' => 'Palladam', 'route' => 'Chicks Route', 'pending_amount' => 250000.00],
        ];

        foreach ($dealers as $d) {
            Dealer::create($d);
        }

        // ── 3. Vendors ──────────────────────────────────────────────────────────
        $vendors = [
            ['firm_name' => 'Vignesh Transport', 'contact_person' => 'Vignesh S', 'phone' => '9000022211', 'location' => 'Tirupur', 'route' => 'Logistic Lane', 'notes' => 'Primary transport partner'],
            ['firm_name' => 'Kovai Pharma Solutions', 'contact_person' => 'Dr. Arvind', 'phone' => '9000022212', 'location' => 'Coimbatore', 'route' => 'City Route A', 'notes' => 'Supplier for vaccines and medicines'],
        ];

        foreach ($vendors as $v) {
            Vendor::create($v);
        }

        // ── 4. Purchases ────────────────────────────────────────────────────────
        $purchaseItems = [
            ['vendor_name' => 'Annai Feeds Pvt Ltd', 'item' => 'Starter Feed', 'quantity' => 100, 'unit' => 'Bags', 'rate' => 1800.00, 'gst_percentage' => 5],
            ['vendor_name' => 'Annai Feeds Pvt Ltd', 'item' => 'Finisher Feed', 'quantity' => 50, 'unit' => 'Bags', 'rate' => 1950.00, 'gst_percentage' => 5],
            ['vendor_name' => 'Bharat Hatcheries', 'item' => 'Day Old Chicks (Cobb 500)', 'quantity' => 5000, 'unit' => 'Nos', 'rate' => 38.00, 'gst_percentage' => 0],
            ['vendor_name' => 'Kovai Pharma Solutions', 'item' => 'RD Vaccine', 'quantity' => 10, 'unit' => 'Vials', 'rate' => 450.00, 'gst_percentage' => 12],
        ];

        foreach ($purchaseItems as $p) {
            $p['date'] = now()->subDays(rand(1, 15));
            $p['payment_mode'] = 'NEFT';
            Purchase::create($p);
        }

        // ── 5. Bills ────────────────────────────────────────────────────────────
        $allCustomers = Customer::all();
        foreach ($allCustomers as $customer) {
            // Weekly Bill
            WeeklyBill::create([
                'customer_id' => $customer->id,
                'period_start' => now()->subDays(14),
                'period_end' => now()->subDays(7),
                'items_description' => 'Broiler Chicken (Standard Size)',
                'quantity_kg' => rand(200, 500),
                'amount' => rand(25000, 60000),
                'status' => 'Pending',
            ]);

            // Daily Bill
            DailyBill::create([
                'customer_id' => $customer->id,
                'date' => now()->subDays(1),
                'items_description' => 'Live Bird Sales',
                'quantity_kg' => rand(50, 150),
                'rate_per_kg' => 110.00,
                'amount' => rand(5000, 16000),
                'status' => 'Generated',
            ]);
        }

        // ── 6. Payments ─────────────────────────────────────────────────────────
        foreach ($allCustomers as $customer) {
            CustomerPayment::create([
                'customer_id' => $customer->id,
                'date' => now()->subDays(2),
                'amount' => 5000.00,
                'payment_mode' => 'UPI',
                'payment_type' => 'Part',
                'balance_after' => $customer->balance - 5000.00,
                'notes' => 'Partial payment received for last week',
            ]);
        }

        $allDealers = Dealer::all();
        foreach ($allDealers as $dealer) {
            DealerPayment::create([
                'dealer_id' => $dealer->id,
                'date' => now()->subDays(3),
                'amount' => 10000.00,
                'payment_mode' => 'NEFT',
                'pending_balance_after' => $dealer->pending_amount - 10000.00,
                'notes' => 'Monthly installment for feed supply',
            ]);
        }

        // ── 7. Expenses & EMIs ───────────────────────────────────────────────────
        $expenseCategories = ['Fuel', 'Salary', 'Transport', 'Utility', 'Misc'];
        for ($i = 0; $i < 10; $i++) {
            Expense::create([
                'date' => now()->subDays(rand(1, 30)),
                'category' => $expenseCategories[array_rand($expenseCategories)],
                'description' => 'Operational expense #' . ($i + 1),
                'amount' => rand(500, 5000),
            ]);
        }

        $emiItems = [
            ['item' => 'Poultry Shed Loan - HDFC', 'amount' => 12500.00, 'due_date' => now()->addDays(5), 'status' => 'Upcoming'],
            ['item' => 'Delivery Van EMI - SBI', 'amount' => 8400.00, 'due_date' => now()->addDays(12), 'status' => 'Upcoming'],
            ['item' => 'Generator Loan - Axis', 'amount' => 4200.00, 'due_date' => now()->subDays(5), 'status' => 'Paid'],
        ];

        foreach ($emiItems as $e) {
            Emi::create($e);
        }
    }
}
