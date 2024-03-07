<?php

namespace App\Http\Controllers;

use App\Models\EngagementRecord;
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
                ->whereYear('sold_at', $currentYear)
                ->whereMonth('sold_at', $month)
                ->sum('selling_price');

            $previousYearSells = Product::where('user_id', $user->id)
                ->where('status', 1)
                ->whereYear('sold_at', $currentYear - 1)
                ->whereMonth('sold_at', $month)
                ->sum('selling_price');
            $this_year = Carbon::now()->format('Y') - 0;
            $prev_year = Carbon::now()->format('Y') - 1;
            return [
                'month' => Carbon::create()->month($month)->shortEnglishMonth,
                $this_year => $currentMonthSells,
                $prev_year => $previousYearSells,
            ];
        });

        return response()->json([
            "analyticsData" => $analyticsData
        ]);
    }
    public function engagementAnalytics()
    {
        $user = auth()->user();
        $currentYear = Carbon::now()->year;
        $engagementData = collect(range(1, 12))->map(function ($month) use ($user, $currentYear) {
            $currentMonthSells = EngagementRecord::join('products', 'engagement_records.product_id', '=', 'products.id')
                ->where('products.user_id', $user->id)
                ->whereYear('engagement_records.created_at', $currentYear)
                ->whereMonth('engagement_records.created_at', $month)
                ->sum('engagement_count');

            $previousYearSells = EngagementRecord::join('products', 'engagement_records.product_id', '=', 'products.id')
                ->where('products.user_id', $user->id)
                ->whereYear('engagement_records.created_at', $currentYear - 1)
                ->whereMonth('engagement_records.created_at', $month)
                ->sum('engagement_count');
            $this_year = Carbon::now()->format('Y') - 0;
            $prev_year = Carbon::now()->format('Y') - 1;
            return [
                'month' => Carbon::create()->month($month)->shortEnglishMonth,
                $this_year => $currentMonthSells,
                $prev_year => $previousYearSells,
            ];
        });

        return response()->json([
            "engagementData" => $engagementData
        ]);
    }
}
