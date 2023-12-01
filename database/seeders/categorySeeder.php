<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\category;

class categorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cat_data = [
            ['category_name' => 'Electronics'],
            ['category_name' => 'Home Appliances'],
            ['category_name' => 'Furniture'],
            ['category_name' => 'Clothing and Accessories'],
            ['category_name' => 'Sports and Fitness'],
            ['category_name' => 'Books and Media'],
            ['category_name' => 'Antiques and Collectibles'],
            ['category_name' => 'Cars'],
            ['category_name' => 'Motorcycles'],
            ['category_name' => 'Scooters'],
            ['category_name' => 'Bicycles'],
            ['category_name' => 'Toys and Games'],
            ['category_name' => 'Musical Instruments'],
            ['category_name' => 'Others'],
        ];
        foreach ($cat_data as $item) {
            category::create($item);
        }
    }
}
