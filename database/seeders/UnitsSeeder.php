<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed units table
        $units = [
            ['name' => 'Pieces', 'short_name' => 'pieces', 'type' => 'Quantity', 'conversion_factor' => 1],
            ['name' => 'Ounces', 'short_name' => 'oz', 'type' => 'Weight', 'conversion_factor' => 28.3495],
            ['name' => 'Pounds', 'short_name' => 'lbs', 'type' => 'Weight', 'conversion_factor' => 453.592],
            ['name' => 'Milligrams', 'short_name' => 'mg', 'type' => 'Weight', 'conversion_factor' => 0.001],
            ['name' => 'Grams', 'short_name' => 'grams', 'type' => 'Weight', 'conversion_factor' => 1],
            ['name' => 'Kilograms', 'short_name' => 'kg', 'type' => 'Weight', 'conversion_factor' => 1000],
            ['name' => 'Carats', 'short_name' => 'ct', 'type' => 'Weight', 'conversion_factor' => 0.2],
            ['name' => 'Inches', 'short_name' => 'in', 'type' => 'Length', 'conversion_factor' => 0.0254],
            ['name' => 'Feet', 'short_name' => 'ft', 'type' => 'Length', 'conversion_factor' => 0.3048],
            ['name' => 'Yards', 'short_name' => 'yd', 'type' => 'Length', 'conversion_factor' => 0.9144],
            ['name' => 'Millimeters', 'short_name' => 'mm', 'type' => 'Length', 'conversion_factor' => 0.001],
            ['name' => 'Centimeters', 'short_name' => 'cm', 'type' => 'Length', 'conversion_factor' => 0.01],
            ['name' => 'Meters', 'short_name' => 'm', 'type' => 'Length', 'conversion_factor' => 1],
            ['name' => 'Sq. Inches', 'short_name' => 'sqin', 'type' => 'Area', 'conversion_factor' => 0.00064516],
            ['name' => 'Sq. Feet', 'short_name' => 'sqft', 'type' => 'Area', 'conversion_factor' => 0.092903],
            ['name' => 'Sq. Centimeters', 'short_name' => 'sqcm', 'type' => 'Area', 'conversion_factor' => 0.0001],
            ['name' => 'Sq. Meters', 'short_name' => 'sqm', 'type' => 'Area', 'conversion_factor' => 1],
            ['name' => 'Fluid Ounces', 'short_name' => 'oz', 'type' => 'Volume', 'conversion_factor' => 0.0295735],
            ['name' => 'Pints', 'short_name' => 'pt', 'type' => 'Volume', 'conversion_factor' => 0.473176],
            ['name' => 'Quarts', 'short_name' => 'qt', 'type' => 'Volume', 'conversion_factor' => 0.946353],
            ['name' => 'Gallons', 'short_name' => 'ga', 'type' => 'Volume', 'conversion_factor' => 3.78541],
            ['name' => 'Milliliters', 'short_name' => 'ml', 'type' => 'Volume', 'conversion_factor' => 0.001],
            ['name' => 'Liters', 'short_name' => 'liters', 'type' => 'Volume', 'conversion_factor' => 1],
        ];

        
        DB::table('units')->insert($units);
    }
}
