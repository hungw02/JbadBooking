<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use Carbon\Carbon;

class UpdateBookingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update booking statuses based on end_time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info('Starting booking status update at: ' . $now->format('Y-m-d H:i:s'));

        // Update single bookings
        $singleBookingsUpdated = SingleBooking::where('status', 'confirmed')
            ->where('end_time', '<', $now)
            ->update(['status' => 'completed']);
        
        $this->info("Updated {$singleBookingsUpdated} single bookings to completed status.");

        // Update subscription bookings that have ended completely
        $subscriptionBookingsUpdated = SubscriptionBooking::where('status', 'confirmed')
            ->where('end_date', '<', $now->format('Y-m-d'))
            ->update(['status' => 'completed']);
        
        $this->info("Updated {$subscriptionBookingsUpdated} subscription bookings to completed status.");

        $this->info('Booking status update completed!');
    }
} 