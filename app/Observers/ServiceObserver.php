<?php

namespace App\Observers;

use App\Models\Service;
use App\Services\TwilioService;

class ServiceObserver
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function created(Service $service)
    {
        $welcomeMessage = "Dear {$service->customer->name}, thank you for visiting our service center. " .
                         "Your next wheel alignment service is scheduled for " .
                         $service->next_service_date->format('Y-m-d') .
                         ". We will remind you before the service. Thank you!";

        $success = $this->twilioService->sendSMS(
            $service->customer->phone,
            $welcomeMessage
        );

        if ($success) {
            $service->update(['welcome_sent' => true]);
        }
    }
}
