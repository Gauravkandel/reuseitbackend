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
}
