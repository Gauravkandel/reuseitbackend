<?php

namespace App\Http\Controllers;

use App\Models\EngagementRecord;
use App\Models\Product;
use App\Models\recommendation;
use Illuminate\Http\Request;


class ViewProductController extends Controller
{


    public function fetchAllData(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $items = Product::with(['user', 'category', 'image'])
            ->whereHas('user', function ($query) {
                $query->where('isBlocked', 0); // Check if the associated user is not blocked
            })
            ->where('status', 0)
            ->inRandomOrder()
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        //pagination starts 1 to 10 and so on
        return response()->json($items);
    }
    public function getIndivProduct($id)
    {
        try {
            $product = Product::with(['category', 'image', 'user'])->findOrFail($id);
            $category = $product->category;

            $data = json_decode($product->extra_features, true);
            $data['fields'] = json_decode($category->fields, true);
            $data['product'] = $product;
            $data = [$data]; //sending data to function getproductData

            return response()->json(['data' => $data], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            // Handling exceptions 
            return response()->json(['error' => 'Something went wrong' . $e], 500);
        }
    }
    public function filter(Request $request)
    {
        $searchTerm = $request->input('search');
        $category = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $user_id = $request->input('user_id');
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
        $products = $query->skip(($page - 1) * $limit)->where('status', 0)->take($limit)->get();

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
}
