<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    public function SendSms(Request $request)
    {
        $number = "+977" . $request->number;
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
    public function VerifyOtp(Request $request)
    {
        $otp = $request->otp;
        $user_id = auth()->user()->id;
        $generated_otp_data = UserOtp::where('user_id', $user_id)->latest()->first();
        $now =  now();
        if ($generated_otp_data->status === 1) {
            return response()->json(["response" => "OTP already verified", "status=>409"], 409);
        }
        if ($now->isAfter($generated_otp_data->expired_at)) {
            return response()->json(["response" => "Your OTP has been expired resend Otp", "status=>410"], 410);
        }
        if ($generated_otp_data->otp != $otp) {
            return response()->json(["response" => "OTP didnot match.", "status=>401"], 401);
        }
        $generated_otp_data->status = 1;
        $generated_otp_data->save();
        $userdata = User::find(auth()->id());
        if ($userdata->verifiedStatus === 0) {
            $userdata->verifiedStatus = 1;
            $userdata->save();
        }
        return response()->json(["response" => "OTP has been verified successfully.", "status=>200"], 200);
    }
}
