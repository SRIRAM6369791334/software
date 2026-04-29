<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        $rate = $this->faker->randomFloat(2, 50, 100);
        $quantity = $this->faker->randomFloat(2, 10, 100);
        $subtotal = $rate * $quantity;
        $gstPercentage = 18;
        $gstAmount = $subtotal * ($gstPercentage / 100);

        return [
            'vendor_id' => Vendor::factory(),
            'vendor_name' => function (array $attributes) {
                return Vendor::find($attributes['vendor_id'])->firm_name;
            },
            'date' => today(),
            'item' => $this->faker->randomElement(['Feed', 'Medicine', 'Chicks', 'Equipments']),
            'quantity' => $quantity,
            'unit' => 'kg',
            'rate' => $rate,
            'gst_percentage' => $gstPercentage,
            'gst_amount' => $gstAmount,
            'total_amount' => $subtotal + $gstAmount,
            'payment_mode' => $this->faker->randomElement(['NEFT', 'Cheque', 'UPI', 'Cash']),
        ];
    }
}
