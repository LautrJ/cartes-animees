<?php

namespace App\Observers;

use App\Enums\ChildSeriesStatus;
use App\Models\Child;
use App\Models\Series;
use Illuminate\Support\Facades\DB;

class ChildObserver
{
    public function created(Child $child): void
    {
        $baseSeries = Series::base()->validated()->active()->get();

        $now = now();

        $rows = $baseSeries->map(fn(Series $series) => [
            'child_id'     => $child->id,
            'series_id'    => $series->id,
            'unlocked_by'  => null,
            'status'       => ChildSeriesStatus::Unlocked->value,
            'unlocked_at'  => $now,
            'completed_at' => null,
            'created_at'   => $now,
            'updated_at'   => $now,
        ])->toArray();

        if (!empty($rows)) {
            DB::table('child_series')->insert($rows);
        }
    }
}
