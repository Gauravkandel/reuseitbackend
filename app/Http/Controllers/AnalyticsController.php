<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function sellAnalytics()
    {
        $user = auth()->user();
        $currentYear = date('Y');

        $analyticsData = DB::table(DB::raw('(SELECT 1 AS month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) AS months'))
            ->leftJoin('products as p', function ($join) use ($user, $currentYear) {
                $join->on(DB::raw('MONTH(p.updated_at)'), '=', 'months.month')
                    ->where('p.user_id', $user->id)
                    ->where('p.status', 1)
                    ->whereYear('p.updated_at', $currentYear);
            })
            ->leftJoin('products as prev_p', function ($join) use ($user, $currentYear) {
                $join->on(DB::raw('MONTH(prev_p.updated_at)'), '=', 'months.month')
                    ->where('prev_p.user_id', $user->id)
                    ->where('prev_p.status', 1)
                    ->whereYear('prev_p.updated_at', $currentYear - 1);
            })
            ->select(
                DB::raw('months.month as month'),
                DB::raw('COALESCE(SUM(p.selling_price), 0) as current_total_selling_price'),
                DB::raw('COALESCE(SUM(prev_p.selling_price), 0) as previous_total_selling_price')
            )
            ->groupBy('months.month')
            ->orderBy('months.month')
            ->get();

        return response()->json([
            "analyticsData" => $analyticsData
        ]);
    }
}
