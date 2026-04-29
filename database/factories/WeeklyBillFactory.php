<?php

namespace Database\Factories;

use App\Models\WeeklyBill;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeeklyBillFactory extends Factory
{
    protected $model = WeeklyBill::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 1000, 5000);
        $gstPercentage = 5;
        $gstAmount = $amount * ($gstPercentage / 100);

        return [
            'customer_id' => Customer::factory(),
            'period_start' => today()->subDays(7),
            'period_end' => today(),
            'items_description' => 'Chicks & Feed',
            'quantity_kg' => $this->faker->randomFloat(2, 500, 2000),
            'amount' => $amount,
            'gst_percentage' => $gstPercentage,
            'gst_amount' => $gstAmount,
            'net_amount' => $amount + $gstAmount,
            'status' => $this->faker->randomElement(['Generated', 'Pending', 'Paid']),
            'payment_mode' => $this->faker->randomElement(['cash', 'credit', 'online']),
        ];
    }
}
