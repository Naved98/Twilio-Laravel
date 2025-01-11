<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class MsgController extends Controller
{
    public function sendWhatsAppMessage(Request $request)
    {
        /*return response()->json(['message' => 'API is working!'])
        ->header('Access-Control-Allow-Origin', 'http://localhost::52680')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');*/

    
          // Validate incoming request
          $validated = $request->validate([
            'to' => 'required|string', // Ensure phone number includes 'whatsapp:' prefix
            'customer_name' => 'required|string',
            'request_id' => 'required|string',
            'service' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'cost' => 'required|string',
            'salon' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        // Fetch Twilio credentials
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER');
        $templateSid = env('TWILIO_TEMPLATE_SID'); // Store template SID in .env file

        // Log Twilio credentials to verify they're correctly loaded
        \Log::info('Twilio Account SID: ' . env('TWILIO_ACCOUNT_SID'));
        \Log::info('Twilio Auth Token: ' . env('TWILIO_AUTH_TOKEN'));
        \Log::info('Twilio WhatsApp Number: ' . env('TWILIO_WHATSAPP_NUMBER'));
        \Log::info('Twilio Template SID: ' . env('TWILIO_TEMPLATE_SID'));
        \Log::info('Sending message from: ' . $from);
        \Log::info('Sending message to: ' . $validated['to']);

        // Check for missing credentials
        if (empty($sid) || empty($token) || empty($from) || empty($templateSid)) {
            return response()->json([
                'success' => false,
                'error' => 'Twilio credentials or template SID are not set properly in the .env file.',
            ], 400);
        }

        // Create Twilio client
        $client = new Client($sid, $token);

        try {
            // Send WhatsApp message using the template
            $message = $client->messages->create(
                $validated['to'], // Recipient's WhatsApp number
                [
                    'from' => $from,
                    'body' =>'',
                    'contentSid' => $templateSid,
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
                'sid' => $message->sid,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in WhatsApp API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400); // Return 500 for server errors
        }
    }
}