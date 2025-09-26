<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('check-bookings-status', function () {
    $this->info('Manually checking and updating booking statuses...');
    
    // Call the scheduled command
    $this->call('bookings:update-status');
    
    $this->info('Completed manual booking status check.');
})->purpose('Manually check and update booking statuses based on end time');
