<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function UsersInfo()
    {
        $month = Carbon::now()->month;
        $users['Total'] = User::where("isAdmin", 0)->count();
        $users['Monthly'] = User::where("isAdmin", 0)->whereMonth('created_at', $month)->count();
        $Revenue['Total'] = Transaction::sum('amount') / 100;
        $Revenue['Monthly'] = Transaction::whereMonth('created_at', $month)->sum('amount') / 100;
        return response()->json([
            "users" => $users,
            "revenue" => $Revenue
        ]);
    }
    public function getallCustomers()
    {
        $user_id = auth()->user()->id;
        $customers = user::where('id', '!=', $user_id)->get();
        return response()->json(['customers' => $customers]);
    }
    public function blockCustomers($id)
    {
        $user_id = $id;
        $user = user::find($user_id);
        if ($user->isBlocked == 1) {
            $user->isBlocked = 0;
            $user->save();
            return response()->json(['message', "Has been Unblocked"]);
        } else {
            $user->isBlocked = 1;
            return response()->json(['message', "Has been blocked"]);
        }
    }
    public function customersAnalytics()
    {
        $user = auth()->user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // $userdata = user::where('id', '!=', $user->id)->get();
        $userdata = collect(range(1, $currentMonth))->map(function ($month) use ($user, $currentYear) {
            $currentMonthCustomers = user::where('id', '!=', $user->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();

            $prevMonthCustomers = user::where('id', '!=', $user->id)
                ->whereYear('created_at', $currentYear - 1)
                ->whereMonth('created_at', $month)
                ->count();
            $this_year = Carbon::now()->format('Y') - 0;
            $prev_year = Carbon::now()->format('Y') - 1;
            return [
                'month' => Carbon::create()->month($month)->shortEnglishMonth,
                $this_year => $currentMonthCustomers + 0,
                $prev_year => $prevMonthCustomers + 0,
            ];
        });
        return response()->json([
            "customersanalytics" => $userdata
        ]);
    }
}
