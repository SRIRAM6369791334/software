<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Vendor;
use App\Models\Route;
use App\Models\DailyBill;
use App\Models\WeeklyBill;
use App\Models\CustomerPayment;
use App\Models\DealerPayment;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\Expense;
use App\Models\Emi;
use Carbon\Carbon;

class ProfitTestSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::now()->format("Y-m-d");
        $route = Route::firstOrCreate(["route_name" => "Test Route"]);

        $customer = Customer::firstOrCreate(
            ["phone" => "9000000001"],
            ["name" => "Test Customer Profit", "address" => "Chennai", "type" => "Retail", "route_id" => $route->id]
        );

        $dealer = Dealer::firstOrCreate(
            ["phone" => "9000000002"],
            ["firm_name" => "Test Dealer Profit", "contact_person" => "Ravi", "route" => "Test Route"]
        );

        $vendor = Vendor::firstOrCreate(
            ["phone" => "9000000003"],
            ["firm_name" => "Test Vendor Profit", "contact_person" => "Kumar", "address" => "Chennai"]
        );

        // ── INFLOW Rs 70,00,000 ─────────────────────────
        // DailyBill (Customer) = Rs 20,00,000  (4 x Rs 5L)
        for ($i = 0; $i < 4; $i++) {
            DailyBill::create([
                "customer_id"    => $customer->id,
                "invoice_number" => "TESTDB" . $i . time(),
                "date"           => Carbon::now()->subDays($i)->format("Y-m-d"),
                "net_amount"     => 500000.00,
                "gst_amount"     => 0,
                "taxable_amount" => 500000.00,
                "payment_mode"   => "Cash",
                "status"         => "Paid",
            ]);
        }

        // WeeklyBill (Dealer) = Rs 15,00,000  (3 x Rs 5L)
        for ($i = 0; $i < 3; $i++) {
            WeeklyBill::create([
                "dealer_id"    => $dealer->id,
                "invoice_no"   => "TESTWB" . $i . time(),
                "period_start" => Carbon::now()->startOfWeek()->format("Y-m-d"),
                "period_end"   => Carbon::now()->format("Y-m-d"),
                "net_amount"   => 500000.00,
                "amount"       => 500000.00,
                "gst_amount"   => 0,
                "payment_mode" => "Cash",
                "status"       => "Paid",
            ]);
        }

        // CustomerPayment = Rs 20,00,000  (4 x Rs 5L)
        for ($i = 0; $i < 4; $i++) {
            CustomerPayment::create([
                "customer_id"  => $customer->id,
                "amount"       => 500000.00,
                "date"         => Carbon::now()->subDays($i)->format("Y-m-d"),
                "payment_mode" => "Cash",
                "note"         => "Test payment " . ($i + 1),
            ]);
        }

        // DealerPayment = Rs 15,00,000  (3 x Rs 5L) -- Dealer pays US = INFLOW
        for ($i = 0; $i < 3; $i++) {
            DealerPayment::create([
                "dealer_id"    => $dealer->id,
                "amount"       => 500000.00,
                "date"         => Carbon::now()->subDays($i)->format("Y-m-d"),
                "payment_mode" => "Cash",
                "note"         => "Dealer payment " . ($i + 1),
            ]);
        }

        // ── OUTFLOW Rs 60,00,000 ─────────────────────────
        // Purchase (Vendor) = Rs 40,00,000  (8 x Rs 5L)
        for ($i = 0; $i < 8; $i++) {
            Purchase::create([
                "vendor_id"      => $vendor->id,
                "invoice_number" => "TESTPUR" . $i . time(),
                "date"           => Carbon::now()->subDays($i)->format("Y-m-d"),
                "item"           => "Broiler Chicken",
                "quantity"       => 1000,
                "rate"           => 500,
                "total_amount"   => 500000.00,
                "gst_amount"     => 0,
                "payment_mode"   => "Cash",
                "status"         => "Paid",
            ]);
        }

        // VendorPayment = Rs 10,00,000  (2 x Rs 5L) -- We pay Vendor = OUTFLOW
        for ($i = 0; $i < 2; $i++) {
            VendorPayment::create([
                "vendor_id"    => $vendor->id,
                "amount"       => 500000.00,
                "date"         => Carbon::now()->subDays($i)->format("Y-m-d"),
                "payment_mode" => "Cash",
                "note"         => "Vendor payment " . ($i + 1),
            ]);
        }

        // Expense = Rs 7,00,000  (7 x Rs 1L)
        $cats = ["Rent","Electricity","Labour","Transport","Feed","Medicine","Misc"];
        foreach ($cats as $cat) {
            Expense::create([
                "category"    => $cat,
                "description" => "Test " . $cat,
                "amount"      => 100000.00,
                "date"        => $today,
            ]);
        }

        // EMI = Rs 3,00,000  (3 x Rs 1L)
        for ($i = 0; $i < 3; $i++) {
            Emi::create([
                "customer_id" => $customer->id,
                "title"       => "Test EMI " . ($i + 1),
                "amount"      => 100000.00,
                "due_date"    => Carbon::now()->subDays($i)->format("Y-m-d"),
                "status"      => "Paid",
            ]);
        }

        $this->command->info("Seeding done. Expected profit = Rs 10,00,000");
    }
}
