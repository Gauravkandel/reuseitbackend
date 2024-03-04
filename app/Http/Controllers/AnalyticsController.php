<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function sellAnalytics()
    {
        $user = auth()->user();
        $currentYear = Carbon::now()->year;

        $analyticsData = collect(range(1, 12))->map(function ($month) use ($user, $currentYear) {
            $currentMonthSells = Product::where('user_id', $user->id)
                ->where('status', 1)
                ->whereYear('updated_at', $currentYear)
                ->whereMonth('updated_at', $month)
                ->sum('selling_price');

            $previousYearSells = Product::where('user_id', $user->id)
                ->where('status', 1)
                ->whereYear('updated_at', $currentYear - 1)
                ->whereMonth('updated_at', $month)
                ->sum('selling_price');

            return [
                'this_year' => Carbon::now()->format('Y') - 0,
                'prev_year' => Carbon::now()->format('Y') - 1,
                'month' => Carbon::create()->month($month)->shortEnglishMonth,
                'current_total_selling_price' => $currentMonthSells,
                'previous_total_selling_price' => $previousYearSells,
            ];
        });

        return response()->json([
            "analyticsData" => $analyticsData
        ]);
    }
}
