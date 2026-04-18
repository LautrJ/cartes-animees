<?php

namespace App\Observers;

use App\Models\CommissionRateHistory;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class SettingObserver
{
    public function updated(Setting $setting): void
    {
        if ($setting->key !== 'commission_rate') {
            return;
        }

        CommissionRateHistory::create([
            'rate'           => (float) $setting->value,
            'effective_from' => now(),
            'created_by'     => Auth::id(),
        ]);
    }
}
