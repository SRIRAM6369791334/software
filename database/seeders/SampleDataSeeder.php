<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Expense;
use App\Models\Emi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Vendors ─────────────────────────────────────────────────────────────
        $vendors = [
            ['firm_name' => 'Annai Feeds Pvt Ltd',    'phone' => '9876543210', 'location' => 'Coimbatore', 'contact_person' => 'Rajan',   'route' => 'North'],
            ['firm_name' => 'Sri Murugan Agencies',    'phone' => '9876543211', 'location' => 'Salem',      'contact_person' => 'Selvam',   'route' => 'East'],
            ['firm_name' => 'Kaveri Poultry Supplies', 'phone' => '9876543212', 'location' => 'Erode',      'contact_person' => 'Priya',    'route' => 'West'],
        ];

        foreach ($vendors as $v) {
            Vendor::firstOrCreate(['firm_name' => $v['firm_name']], $v);
        }

        // ── Customers ────────────────────────────────────────────────────────────
        $customers = [
            ['name' => 'Ravi Kumar',    'phone' => '9000000001', 'address' => 'Chennai',    'route' => 'North', 'type' => 'Wholesale', 'balance' => 5000],
            ['name' => 'Meena Stores',  'phone' => '9000000002', 'address' => 'Madurai',    'route' => 'South', 'type' => 'Retail',    'balance' => 1500],
            ['name' => 'Karthik Deals', 'phone' => '9000000003', 'address' => 'Trichy',     'route' => 'East',  'type' => 'Wholesale', 'balance' => 3200],
            ['name' => 'Lakshmi Mart',  'phone' => '9000000004', 'address' => 'Coimbatore', 'route' => 'West',  'type' => 'Retail',    'balance' => 800],
        ];

        $customerIds = [];
        foreach ($customers as $c) {
            $customer = Customer::firstOrCreate(['phone' => $c['phone']], $c);
            $customerIds[] = $customer->id;
        }

        // ── Purchases ────────────────────────────────────────────────────────────
        $purchaseItems = [
            ['vendor_name' => 'Annai Feeds Pvt Ltd',    'item' => 'Starter Feed',    'quantity' => 100, 'unit' => 'Bags', 'rate' => 1800, 'gst_percentage' => 5,  'payment_mode' => 'NEFT'],
            ['vendor_name' => 'Sri Murugan Agencies',    'item' => 'Grower Feed',     'quantity' => 50,  'unit' => 'Bags', 'rate' => 1650, 'gst_percentage' => 5,  'payment_mode' => 'UPI'],
            ['vendor_name' => 'Kaveri Poultry Supplies', 'item' => 'Vaccines',        'quantity' => 200, 'unit' => 'Vials','rate' => 45,   'gst_percentage' => 12, 'payment_mode' => 'Cash'],
            ['vendor_name' => 'Annai Feeds Pvt Ltd',    'item' => 'Finisher Feed',   'quantity' => 80,  'unit' => 'Bags', 'rate' => 1700, 'gst_percentage' => 5,  'payment_mode' => 'Cheque'],
            ['vendor_name' => 'Sri Murugan Agencies',    'item' => 'Poultry Vitamins','quantity' => 100, 'unit' => 'Btls', 'rate' => 120,  'gst_percentage' => 12, 'payment_mode' => 'NEFT'],
        ];

        foreach ($purchaseItems as $i => $p) {
            $rate   = $p['rate'];
            $qty    = $p['quantity'];
            $gstPct = $p['gst_percentage'];
            $subTotal   = $rate * $qty;
            $gstAmount  = round($subTotal * $gstPct / 100, 2);
            $total      = $subTotal + $gstAmount;

            DB::table('purchases')->insertOrIgnore([
                'vendor_name'    => $p['vendor_name'],
                'item'           => $p['item'],
                'quantity'       => $qty,
                'unit'           => $p['unit'],
                'rate'           => $rate,
                'gst_percentage' => $gstPct,
                'gst_amount'     => $gstAmount,
                'total_amount'   => $total,
                'payment_mode'   => $p['payment_mode'],
                'date'           => now()->subDays(rand(1, 30))->toDateString(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // ── Expenses ─────────────────────────────────────────────────────────────
        $expenseCategories = ['Fuel', 'Salary', 'Transport', 'Utility', 'Misc'];
        for ($i = 0; $i < 10; $i++) {
            Expense::create([
                'date'        => now()->subDays(rand(1, 30)),
                'category'    => $expenseCategories[array_rand($expenseCategories)],
                'description' => 'Operational expense #' . ($i + 1),
                'amount'      => rand(500, 5000),
            ]);
        }

        // ── EMIs ─────────────────────────────────────────────────────────────────
        $emiItems = [
            ['loan_name' => 'Poultry Shed Loan - HDFC', 'bank_name' => 'HDFC Bank', 'amount' => 12500.00, 'due_date' => now()->addDays(5),  'status' => 'Upcoming'],
            ['loan_name' => 'Delivery Van EMI - SBI',   'bank_name' => 'SBI',       'amount' => 8400.00,  'due_date' => now()->addDays(12), 'status' => 'Upcoming'],
            ['loan_name' => 'Generator Loan - Axis',    'bank_name' => 'Axis Bank', 'amount' => 4200.00,  'due_date' => now()->subDays(5),  'status' => 'Paid'],
        ];

        foreach ($emiItems as $e) {
            Emi::firstOrCreate(['loan_name' => $e['loan_name']], $e);
        }

        // ── Daily Bills ──────────────────────────────────────────────────────────
        if (!empty($customerIds)) {
            for ($i = 0; $i < 8; $i++) {
                $qty    = rand(50, 200);
                $rate   = rand(140, 190);
                $amount = $qty * $rate;

                DB::table('daily_bills')->insert([
                    'customer_id'       => $customerIds[array_rand($customerIds)],
                    'date'              => now()->subDays(rand(1, 15))->toDateString(),
                    'items_description' => 'Broiler Chicken',
                    'quantity_kg'       => $qty,
                    'rate_per_kg'       => $rate,
                    'amount'            => $amount,
                    'status'            => ['Pending', 'Paid'][rand(0, 1)],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }

        $this->command->info('✅ Sample data seeded: vendors, customers, purchases, expenses, EMIs, daily bills.');
    }
}
