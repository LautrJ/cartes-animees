<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Notifications\NoProgressNotification;
use Illuminate\Console\Command;

class SendNoProgressNotifications extends Command
{
    protected $signature = 'notifications:no-progress';

    protected $description = 'Envoie un mail aux parents inactifs depuis 2 semaines (last_login_at)';

    public function handle(): int
    {
        $children = Child::query()
            ->whereHas('subscriptions', fn ($q) => $q->accessible())
            ->whereHas('parent', fn ($q) => $q
                ->where(fn ($q) => $q
                    ->whereNull('last_login_at')
                    ->orWhere('last_login_at', '<', now()->subWeeks(2))
                )
            )
            ->with('parent')
            ->get();

        $count = 0;

        foreach ($children as $child) {
            $child->parent->notify(new NoProgressNotification(
                childFirstName: $child->first_name,
            ));
            $count++;
        }

        $this->info("✅ {$count} notification(s) envoyée(s).");

        return self::SUCCESS;
    }
}
