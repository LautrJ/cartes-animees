<?php

namespace App\Http\Controllers\Api;

use App\Enums\ChildSeriesStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Child;
use App\Models\Series;
use App\Notifications\SeriesCompletedNotification;
use App\Notifications\SeriesUnlockedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildSeriesController extends Controller
{
    public function store(Request $request, Child $child, Series $series): JsonResponse
    {
        $isPatient = $request->user()->activePatients()
            ->where('children.id', $child->id)
            ->exists();

        if (! $isPatient) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        if (! $series->is_validated || ! $series->is_active) {
            return ApiResponse::error(__('api.child_series.series_not_found'), 404);
        }

        $alreadyUnlocked = $child->series()
            ->where('series.id', $series->id)
            ->exists();

        if ($alreadyUnlocked) {
            return ApiResponse::error(__('api.child_series.already_unlocked'), 409);
        }

        $child->series()->attach($series->id, [
            'unlocked_by' => $request->user()->id,
            'status' => ChildSeriesStatus::Unlocked,
            'unlocked_at' => now(),
        ]);

        $child->parent->notify(
            new SeriesUnlockedNotification($child, $series)
        );

        return ApiResponse::success(['message' => __('api.child_series.unlocked_success')], 201);
    }

    public function update(Request $request, Child $child, Series $series): JsonResponse
    {
        $isPatient = $request->user()->activePatients()
            ->where('children.id', $child->id)
            ->exists();

        if (! $isPatient) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $childSeries = $child->series()
            ->where('series.id', $series->id)
            ->first();

        if (! $childSeries) {
            return ApiResponse::error(__('api.common.series_not_unlocked_for_child'), 404);
        }

        if ($childSeries->pivot->status === ChildSeriesStatus::Completed->value) {
            return ApiResponse::error(__('api.child_series.already_completed'), 409);
        }

        $child->series()->updateExistingPivot($series->id, [
            'status' => ChildSeriesStatus::Completed,
            'completed_at' => now(),
        ]);

        $child->parent->notify(
            new SeriesCompletedNotification($child, $series)
        );

        return ApiResponse::success(['message' => __('api.child_series.completed_success')]);
    }

    public function destroy(Request $request, Child $child, Series $series): JsonResponse
    {
        $isPatient = $request->user()->activePatients()
            ->where('children.id', $child->id)
            ->exists();

        if (! $isPatient) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $child->series()->detach($series->id);

        return ApiResponse::success(null, 204);
    }
}
