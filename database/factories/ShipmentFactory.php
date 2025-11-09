<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Shipment;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition()
    {
        $statuses = ['pending','in_transit','delivered','cancelled'];
        return [
            'tracking_number' => strtoupper(Str::random(12)),
            'order_reference' => 'ORD-' . $this->faker->unique()->numberBetween(1000,9999),
            'carrier' => $this->faker->randomElement(['UPS','USPS','DHL','FedEx']),
            'service' => $this->faker->randomElement(['Ground','Express','Priority']),
            'status' => $this->faker->randomElement($statuses),
            'weight_kg' => $this->faker->randomFloat(2, 0.1, 20),
            'destination' => $this->faker->city . ', ' . $this->faker->country,
            'created_at' => $this->faker->dateTimeBetween('-10 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
