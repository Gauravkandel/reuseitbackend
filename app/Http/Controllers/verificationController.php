<?php

namespace App\Http\Controllers;

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
        $otp = rand(9999, 99999);
        $message = 'Your ReUseIt Seller Verification code is ' . $otp;
        try {
            $accountSid = "ACd64800b5fb8b0e0e92f4da5da51ec87d";
            $authToken = "542393530f2a292c5717586a5f8810b4";
            $fromNumber = "+16788947857";
            // Check if credentials are present
            if (!$accountSid || !$authToken || !$fromNumber) {
                throw new Exception("Twilio credentials are missing");
            }

            $client = new Client($accountSid, $authToken);
            $message = $client->messages->create($number, [
                'from' => $fromNumber,
                'body' => $message
            ]);
            // Check the status of the message
            if ($message->status == 'failed') {
                throw new Exception("Failed to send SMS. Twilio status: " . $message->status);
            }

            return response()->json(["success" => "SMS sent successfully"])->cookie('auth-token', "gaurav", 60);;
        } catch (\Exception $e) {
            return response()->json(["error" => "error occurred " . $e->getMessage()]);
        }
    }
}
