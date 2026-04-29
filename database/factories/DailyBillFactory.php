<?php

namespace Database\Factories;

use App\Models\DailyBill;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyBillFactory extends Factory
{
    protected $model = DailyBill::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 100, 2000);
        $gstPercentage = $this->faker->randomElement([0, 5, 12, 18]);
        $gstAmount = $amount * ($gstPercentage / 100);

        return [
            'customer_id' => Customer::factory(),
            'date' => today(),
            'items_description' => $this->faker->sentence(),
            'quantity_kg' => $this->faker->randomFloat(2, 1, 50),
            'rate_per_kg' => $this->faker->randomFloat(2, 50, 100),
            'amount' => $amount,
            'gst_percentage' => $gstPercentage,
            'gst_amount' => $gstAmount,
            'net_amount' => $amount + $gstAmount,
            'payment_mode' => $this->faker->randomElement(['cash', 'credit']),
            'status' => 'Paid',
        ];
    }
}
