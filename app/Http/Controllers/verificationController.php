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
            $accountSid = "ACd1065f5faf96c3b98197c4cdba16160e";
            $authToken = "b370020fed5a0114d59e9c68ec9cb2d1";
            $fromNumber = "+15733194051";
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

            return response()->json(["success" => "SMS sent successfully"]);
        } catch (\Exception $e) {
            return response()->json(["error" => "error occurred " . $e->getMessage()]);
        }
    }
}
