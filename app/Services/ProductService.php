<?php
// app/Services/ProductService.php
namespace App\Services;

use App\Models\Electronic;
use App\Models\HomeAppliance;
use App\Models\Furniture;
use App\Models\Clothing;
use App\Models\Sport;
use App\Models\Book;
use App\Models\Antique;
use App\Models\Car;
use App\Models\Bicycle;
use App\Models\Motorcycle;
use App\Models\Scooter;
use App\Models\Toy;
use App\Models\Music;

class ProductService
{
    public function getProductData($category, $id)
    {
        switch ($category->category_name) {
            case "Electronics":
                return Electronic::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Home Appliances":
                return HomeAppliance::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Furniture":
                return Furniture::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Clothing and Accessories":
                return Clothing::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Sports and Fitness":
                return Sport::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Books and Media":
                return Book::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Antiques and Collectibles":
                return Antique::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Cars":
                return Car::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Bicycles":
                return Bicycle::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Motorcycles":
                return Motorcycle::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Scooters":
                return Scooter::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Toys and Games":
                return Toy::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Musical Instruments":
                return Music::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            default:
                return null;
        }
    }
}
