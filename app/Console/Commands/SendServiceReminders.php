<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\TwilioService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendServiceReminders extends Command
{
    protected $signature = 'reminders:send {--debug : Show debug information}';
    protected $description = 'Send SMS reminders for upcoming services';

    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        parent::__construct();
        $this->twilioService = $twilioService;
    }

    public function handle()
    {
        $tomorrow = Carbon::tomorrow();
        $debug = $this->option('debug');

        if ($debug) {
            $this->info("Looking for services scheduled for: " . $tomorrow->format('Y-m-d'));

            // Show all services
            $allServices = Service::with('customer')->get();
            $this->info("\nAll services in database:");
            foreach ($allServices as $service) {
                $this->info("ID: {$service->id} | Next Service: {$service->next_service_date} | Reminder Sent: {$service->reminder_sent} | Customer: {$service->customer->name}");
            }
        }

        $services = Service::query()
            ->whereDate('next_service_date', $tomorrow->format('Y-m-d'))
            ->where('reminder_sent', false)
            ->with('customer')
            ->get();

        if ($debug) {
            $this->info("\nQuery conditions:");
            $this->info("Next service date: " . $tomorrow->format('Y-m-d'));
            $this->info("Reminder sent: false");
            $this->info("\nFound services: " . $services->count());
        }

        if ($services->isEmpty()) {
            $this->info("No reminders to send for tomorrow.");
            return 0;
        }

        foreach ($services as $service) {
            $reminderMessage = "Dear {$service->customer->name}, this is a reminder that your vehicle " .
                             "{$service->customer->vehicle_number} is due for wheel alignment tomorrow " .
                             $service->next_service_date->format('Y-m-d') .
                             ". Please visit our center at your convenient time. Thank you!";

            if ($debug) {
                $this->info("\nAttempting to send reminder to:");
                $this->info("Customer: {$service->customer->name}");
                $this->info("Phone: {$service->customer->phone}");
                $this->info("Message: {$reminderMessage}");
            }

            $success = $this->twilioService->sendSMS(
                $service->customer->phone,
                $reminderMessage
            );

            if ($success) {
                $service->update(['reminder_sent' => true]);
                $this->info("✓ Reminder sent successfully to {$service->customer->name}");
            } else {
                $this->error("✗ Failed to send reminder to {$service->customer->name}");
            }
        }

        return 0;
    }
}
