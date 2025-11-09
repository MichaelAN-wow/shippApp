<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('contacts')->insert([
            [
                'name' => 'Hans Muller',
                'street' => 'Berliner Str 45',
                'city' => 'Berlin',
                'state' => 'BE',
                'zip' => '10115',
                'country' => 'DE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'US',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
