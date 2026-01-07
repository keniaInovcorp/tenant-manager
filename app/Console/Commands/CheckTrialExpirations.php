<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\TrialExpiringNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTrialExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for trials expiring in 1 day and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for trials expiring in 1 day...');

        $tomorrow = now()->addDay()->startOfDay();
        $dayAfterTomorrow = now()->addDay()->endOfDay();

        $expiringSubscriptions = Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [$tomorrow, $dayAfterTomorrow])
            ->where('trial_notification_sent', false)
            ->with(['tenant.owner', 'plan'])
            ->get();

        if ($expiringSubscriptions->isEmpty()) {
            $this->info('No trials expiring in 1 day.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($expiringSubscriptions as $subscription) {
            try {
                $owner = $subscription->tenant->owner;

                if ($owner) {
                    $owner->notify(new TrialExpiringNotification($subscription));

                    $subscription->update(['trial_notification_sent' => true]);

                    $this->info("Notification sent to {$owner->email} for tenant: {$subscription->tenant->name}");
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to send trial expiration notification', [
                    'subscription_id' => $subscription->id,
                    'tenant_id' => $subscription->tenant_id,
                    'error' => $e->getMessage(),
                ]);

                $this->error("Failed to send notification for tenant: {$subscription->tenant->name}");
            }
        }

        $this->info("Total notifications sent: {$count}");

        return Command::SUCCESS;
    }
}
