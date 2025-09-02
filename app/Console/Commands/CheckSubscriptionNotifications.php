<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Client\SubscriptionsController;

class CheckSubscriptionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and create subscription expiration notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking subscription notifications...');
        
        try {
            $controller = new SubscriptionsController();
            $controller->createSubscriptionNotifications();
            
            $this->info('Subscription notifications checked successfully.');
        } catch (\Exception $e) {
            $this->error('Error checking subscription notifications: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
