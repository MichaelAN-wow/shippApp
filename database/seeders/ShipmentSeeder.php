<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Shipment;

class ShipmentSeeder extends Seeder
{
    public function run()
    {
        Shipment::factory()->count(30)->create();
    }
}
