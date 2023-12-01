<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\product;
use App\Models\recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function recommend(Request $request)
    {
        $prod_id = $request->product_id;
        $user_id = $request->user_id;
        $product = product::findOrFail($prod_id);
        $category = $product->category->category_name;
        $seller_id = $product->user_id;
        if ($user_id) {
            if ($seller_id != $user_id) {
                $userRecommend = recommendation::where('user_id', $user_id)->get();
                $categoryExists = false;
                foreach ($userRecommend as $recommendation_data) {
                    if ($recommendation_data->category_name === $category) {
                        $recommendation_data->count = $recommendation_data->count + 1;
                        $recommendation_data->save();
                        $categoryExists = true;
                        break;
                    }
                }
                if (!$categoryExists) {
                    $recommend = new recommendation();
                    $recommend->user_id = $user_id;
                    $recommend->category_name = $category;
                    $recommend->save();
                    return "saved";
                }
                return "increased";
            }
        }

        return "error";
    }
    public function getrecommended(Request $request)
    {
        $user_id = $request->input('user_id');

        if (!$user_id) {
            return response()->json(['recommendations' => null], 400);
        }

        $categoryRecommendations = Recommendation::where('user_id', $user_id)
            ->orderByDesc('count')
            ->take(5)
            ->pluck('category_name');

        $products = Product::with('image')->where('user_id', '!=', $user_id)
            ->whereIn('category_id', function ($query) use ($categoryRecommendations) {
                $query->select('id')
                    ->from('categories')
                    ->whereIn('category_name', $categoryRecommendations);
            })
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return response()->json(['recommendations' => $products], 200);
    }
}
