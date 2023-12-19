<?php

namespace App\Http\Controllers;

use App\Models\antique;
use App\Models\bicycle;
use App\Models\book;
use App\Models\car;
use App\Models\clothing;
use App\Models\electronic;
use App\Models\Furniture;
use App\Models\HomeAppliance;
use App\Models\motorcycle;
use App\Models\music;
use App\Models\Product;
use App\Models\scooter;
use App\Models\sport;
use App\Models\toy;
use App\Models\vehicle;
use Illuminate\Http\Request;

class ViewProductController extends Controller
{
    public function fetchAllData(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $items = product::with(['category', 'image'])->skip(($page - 1) * $limit)->where('status', 0)->inRandomOrder()->take($limit)->get();
        //pagination starts 1 to 10 and so on
        return response()->json($items);
    }

    public function getIndivProduct($id)
    {
        try {
            $product = Product::with(['category', 'image'])->findOrFail($id);

            $category = $product->category->category_name;

            $data = $this->getProductData($category, $id); //sending data to function getproductData

            return response()->json(['data' => $data], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            // Handling exceptions 
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    public function filter(Request $request)
    {
        $searchTerm = $request->input('search');
        $category = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $query = Product::with(['category', 'image']);

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('pname', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('category', function ($query) use ($searchTerm) {
                        $query->where('category_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhere('Province', 'like', '%' . $searchTerm . '%')
                    ->orWhere('District', 'like', '%' . $searchTerm . '%')
                    ->orWhere('Municipality', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category_name', $category);
            });
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $products = $query->skip(($page - 1) * $limit)->take($limit)->get();

        return response()->json($products);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        //search data as category, location, and name
        $results = Product::where('pname', 'like', '%' . $searchTerm . '%')
            ->orWhereHas('category', function ($query) use ($searchTerm) {
                $query->where('category_name', 'like', '%' . $searchTerm . '%');
            })
            ->orWhere('Province', 'like', '%' . $searchTerm . '%')
            ->orWhere('District', 'like', '%' . $searchTerm . '%')
            ->orWhere('Municipality', 'like', '%' . $searchTerm . '%')
            ->with(['category', 'image'])->skip(($page - 1) * $limit)->take($limit)->get();
        //sending data to front end for display
        return response()->json($results);
    }






















    private function getProductData($category, $id)
    {
        switch ($category) {
            case "Electronics":
                return electronic::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Home Appliances":
                return HomeAppliance::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Furniture":
                return Furniture::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Clothing and Accessories":
                return clothing::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Sports and Fitness":
                return sport::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Books and Media":
                return book::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Antiques and Collectibles":
                return antique::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Cars":
                return car::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Bicycles":
                return bicycle::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Motorcycles":
                return motorcycle::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Scooters":
                return scooter::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Toys and Games":
                return toy::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            case "Musical Instruments":
                return music::with(['product', 'product.image', 'product.category', 'product.user'])->where('product_id', $id)->get();
            default:
                return null;
        }
    }
}
