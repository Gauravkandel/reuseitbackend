<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class BuyPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function displayBuyProduct()
    {
        $product = product::where('user_id', auth()->user()->id)
            ->where("status", 0)
            ->where('featured_package', 0)->get();
        return response()->json(["productsBuy" => $product]);
    }
}
