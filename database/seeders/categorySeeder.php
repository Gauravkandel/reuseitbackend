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
            [
                'category_name' => 'Electronics',
                'function_name' => 'electronics'
            ],
            [
                'category_name' => 'Home Appliances',
                'function_name' => 'homeAppliances'
            ],
            [
                'category_name' => 'Furniture',
                'function_name' => 'furnitures'
            ],
            [
                'category_name' => 'Clothing and Accessories',
                'function_name' => 'clothing'
            ],
            [
                'category_name' => 'Sports and Fitness',
                'function_name' => 'sports'
            ],
            [
                'category_name' => 'Books and Media',
                'function_name' => 'books'
            ],
            [
                'category_name' => 'Antiques and Collectibles',
                'function_name' => 'antiques'
            ],
            [
                'category_name' => 'Cars',
                'function_name' => 'cars'
            ],
            [
                'category_name' => 'Motorcycles',
                'function_name' => 'motorcycle'
            ],
            [
                'category_name' => 'Scooters',
                'function_name' => 'scooter'
            ],
            [
                'category_name' => 'Bicycles',
                'function_name' => 'bicycle'
            ],
            [
                'category_name' => 'Toys and Games',
                'function_name' => 'toys'
            ],
            [
                'category_name' => 'Musical Instruments',
                'function_name' => 'music'
            ],
            [
                'category_name' => 'Others',
                'function_name' => 'others'
            ],
        ];
        foreach ($cat_data as $item) {
            category::create($item);
        }
    }
}
