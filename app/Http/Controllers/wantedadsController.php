<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\wantedad;
use App\Notifications\wantedadsnotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class wantedadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function addWantedAds(Request $request)
    {
        $adsdata = $request->all();
        $validationRules = [
            'user_id' => 'required|exists:users,id',
            'adname' => 'required|string|max:255',
            'description' => 'required|string',
            'Province' => 'required|string',
            'District' => 'required|string',
            'Municipality' => 'required|string',
        ];
        $request->validate($validationRules);
        $ads =  wantedad::create($adsdata);
        $users = User::where('Province', $ads->Province)
            ->where('Municipality', $ads->Municipality)
            ->where('District', $ads->District)->get();

        foreach ($users as $user) {
            Notification::send($user, new wantedadsnotification($user->name));
        }
        return response()->json(['success' => 'successful', 'adsdata' => $ads, "status" => 200], 200);
    }
    public function getwantedads()
    {
        $wantedadsdata = wantedad::orderBy('created_at', 'asc')->with('user')->get();
        return response()->json(['WantedAds' => $wantedadsdata]);
    }
}
