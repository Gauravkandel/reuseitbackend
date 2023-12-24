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
                'function_name' => 'electronics',
                'fields' => json_encode(['type_of_electronic', 'brand', 'model', 'condition', 'warranty_information'])
            ],
            [
                'category_name' => 'Home Appliances',
                'function_name' => 'homeappliances',
                'fields' => json_encode(['type_of_appliance', 'brand', 'model', 'capacity', 'features', 'condition', 'warranty_information'])

            ],
            [
                'category_name' => 'Furniture',
                'function_name' => 'furnitures',
                'fields' => json_encode(['type_of_furniture', 'material', 'dimensions', 'color', 'style', 'condition', 'assembly_required'])
            ],
            [
                'category_name' => 'Clothing and Accessories',
                'function_name' => 'clothings',
                'fields' => json_encode(['type_of_clothing_accessory', 'size', 'color', 'brand', 'material', 'condition', 'care_instructions'])

            ],
            [
                'category_name' => 'Sports and Fitness',
                'function_name' => 'sports',
                'fields' => json_encode(['type_of_equipment', 'brand', 'condition', 'size_weight', 'features', 'suitable_sport_activity', 'warranty_information', 'usage_instructions'])
            ],
            [
                'category_name' => 'Books and Media',
                'function_name' => 'books',
                'fields' => json_encode(['title', 'author_artist', 'genre', 'format', 'condition', 'edition', 'isbn_upc', 'warranty_information', 'description'])
            ],
            [
                'category_name' => 'Antiques and Collectibles',
                'function_name' => 'antiques',
                'fields' => json_encode(['type_of_item', 'era_period', 'material', 'condition', 'provenance_location', 'rarity', 'historical_significance', 'certification'])
            ],
            [
                'category_name' => 'Cars',
                'function_name' => 'cars',
                'fields' => json_encode(['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'fuel_type', 'owner', 'transmission_type'])
            ],
            [
                'category_name' => 'Motorcycles',
                'function_name' => 'motorcycles',
                'fields' => json_encode(['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'])
            ],
            [
                'category_name' => 'Scooters',
                'function_name' => 'scooters',
                'fields' => json_encode(['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'])
            ],
            [
                'category_name' => 'Bicycles',
                'function_name' => 'bicycles',
                'fields' => json_encode(['brand'])
            ],
            [
                'category_name' => 'Toys and Games',
                'function_name' => 'toys',
                'fields' => json_encode(['type_of_toy_game', 'age_group', 'brand', 'condition', 'description', 'safety_information', 'assembly_required', 'recommended_use'])
            ],
            [
                'category_name' => 'Musical Instruments',
                'function_name' => 'musics',
                'fields' => json_encode(['type_of_instrument', 'brand', 'condition', 'material', 'accessories_included', 'sound_characteristics'])
            ],
            [
                'category_name' => 'Others',
                'function_name' => 'others',
                'fields' => json_encode([])
            ],
        ];
        foreach ($cat_data as $item) {
            category::create($item);
        }
    }
}
