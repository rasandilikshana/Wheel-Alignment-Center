<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withSchedule(function(Schedule $schedule) {
        // Send reminders at 9 AM daily
        $schedule->command('reminders:send')
                ->dailyAt('09:00')
                ->appendOutputTo(storage_path('logs/scheduler.log'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
