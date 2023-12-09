<?php

namespace App\Http\Controllers;

use App\Models\UserOtp;
use Exception;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class verificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function SendSms(Request $req)
    {
        $number = "+977" . $req->number;
        $userotp = $this->generateOtp();
        $result = $userotp->sendSMS($number);
        return response()->json(['result' => $result]);
    }
    public function generateOtp()
    {
        $id = auth()->user()->id;
        $userotp = UserOtp::where('user_id', $id)->latest()->first();
        $now = now();
        if ($userotp && $now->isBefore($userotp->expired_at)) {
            return $userotp;
        }
        return UserOtp::create([
            'user_id' => $id,
            'otp' => rand(9999, 99999),
            'expired_at' => $now->addMinutes(2),
        ]);
    }
}
