<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppMessageController extends Controller
{
    public function sendWhatsAppMessage(Request $request)
    {
        // CORS headers (optional if handled in middleware)
      /*  header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");*/

        // For preflight requests (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            return response()->json(['message' => 'Preflight request handled.'], 200);
        }

        // Validate incoming request
        $validated = $request->validate([
            'to' => 'required|string',
            'customer_name' => 'required|string',
            'request_id' => 'required|string',
            'service' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'cost' => 'required|string',
            'salon' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        // Twilio credentials from .env
        $accountSid = env('TWILIO_ACCOUNT_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER');
        $templateSid = 'HX4ba5e82ea3ae15a01a24fb6e938f43b2';

        if (!$accountSid || !$authToken || !$twilioNumber) {
            return response()->json([
                'success' => false,
                'error' => 'Twilio credentials are not properly configured in the .env file.',
            ], 500);
        }

        try {
            // Create Twilio client
            $client = new Client($accountSid, $authToken);

            // Send WhatsApp message
            $message = $client->messages->create(
                'whatsapp:' . $validated['to'], // Recipient's WhatsApp number
                [
                    'from' => 'whatsapp:' . $twilioNumber, // Twilio WhatsApp number
                    'contentSid' => $templateSid, // Template SID
                    'contentVariables' => json_encode([
                        '1' => $validated['customer_name'],
                        '2' => $validated['request_id'],
                        '3' => $validated['service'],
                        '4' => $validated['date'],
                        '5' => $validated['time'],
                        '6' => $validated['cost'],
                        '7' => $validated['salon'],
                        '8' => $validated['phone_number'],
                    ]),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'sid' => $message->sid,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
