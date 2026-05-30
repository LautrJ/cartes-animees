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
            return ApiResponse::error('Accès refusé.', 403);
        }

        if (! $series->is_validated || ! $series->is_active) {
            return ApiResponse::error('Série introuvable.', 404);
        }

        $alreadyUnlocked = $child->series()
            ->where('series.id', $series->id)
            ->exists();

        if ($alreadyUnlocked) {
            return ApiResponse::error('Cette série est déjà débloquée pour cet enfant.', 409);
        }

        $child->series()->attach($series->id, [
            'unlocked_by' => $request->user()->id,
            'status' => ChildSeriesStatus::Unlocked,
            'unlocked_at' => now(),
        ]);

        $child->parent->notify(
            new SeriesUnlockedNotification($child, $series)
        );

        return ApiResponse::success(['message' => 'Série débloquée avec succès.'], 201);
    }

    public function update(Request $request, Child $child, Series $series): JsonResponse
    {
        $isPatient = $request->user()->activePatients()
            ->where('children.id', $child->id)
            ->exists();

        if (! $isPatient) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $childSeries = $child->series()
            ->where('series.id', $series->id)
            ->first();

        if (! $childSeries) {
            return ApiResponse::error('Cette série n\'est pas débloquée pour cet enfant.', 404);
        }

        if ($childSeries->pivot->status === ChildSeriesStatus::Completed->value) {
            return ApiResponse::error('Cette série est déjà complétée.', 409);
        }

        $child->series()->updateExistingPivot($series->id, [
            'status' => ChildSeriesStatus::Completed,
            'completed_at' => now(),
        ]);

        $child->parent->notify(
            new SeriesCompletedNotification($child, $series)
        );

        return ApiResponse::success(['message' => 'Série marquée comme complétée.']);
    }

    public function destroy(Request $request, Child $child, Series $series): JsonResponse
    {
        $isPatient = $request->user()->activePatients()
            ->where('children.id', $child->id)
            ->exists();

        if (! $isPatient) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $child->series()->detach($series->id);

        return ApiResponse::success(null, 204);
    }
}
