<?php

// app/Services/TwilioService.php
namespace App\Services;

use Twilio\Rest\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
        $this->fromNumber = config('services.twilio.number');
    }

    public function sendSMS(string $to, string $message): bool
    {
        try {
            $this->client->messages->create(
                $to,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );
            return true;
        } catch (Exception $e) {
            Log::error('Twilio SMS Error: ' . $e->getMessage());
            return false;
        }
    }
}
