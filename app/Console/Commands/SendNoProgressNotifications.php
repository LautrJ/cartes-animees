<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Notifications\NoProgressNotification;
use Illuminate\Console\Command;

class SendNoProgressNotifications extends Command
{
    protected $signature = 'notifications:no-progress';

    protected $description = 'Envoie un mail aux parents dont l\'enfant n\'a pas eu d\'activité depuis 7 jours';

    public function handle(): int
    {
        $children = Child::query()
            ->whereHas('subscriptions', fn ($q) => $q->accessible())
            ->whereHas('parent', fn ($q) => $q
                ->where(fn ($q) => $q
                    ->whereNull('last_login_at')
                    ->orWhere('last_login_at', '<', now()->subDays(7))
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
