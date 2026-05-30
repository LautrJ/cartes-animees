<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Child;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $series = $child->series()
            ->where('is_active', true)
            ->withPivot(['status', 'unlocked_at', 'completed_at'])
            ->get()
            ->map(fn ($series) => [
                'id' => $series->id,
                'name' => $series->name,
                'description' => $series->description,
                'thumbnail' => $series->thumbnail_path,
                'is_base' => $series->is_base,
                'status' => $series->pivot->status,
                'unlocked_at' => $series->pivot->unlocked_at,
                'completed_at' => $series->pivot->completed_at,
            ]);

        return ApiResponse::success($series);
    }

    public function show(Request $request, Child $child, Series $series): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $hasAccess = $child->series()
            ->where('series.id', $series->id)
            ->exists();

        if (! $hasAccess) {
            return ApiResponse::error('Cette série n\'est pas débloquée pour cet enfant.', 403);
        }

        $cards = $series->cards()
            ->orderBy('series_cards.order')
            ->get()
            ->map(fn ($card) => [
                'id' => $card->id,
                'name' => $card->name,
                'drawn_animation_path' => $card->drawn_animation_path,
                'real_animation_path' => $card->real_animation_path,
                'sound_path' => $card->sound_path,
                'width' => $card->width,
                'height' => $card->height,
                'duration' => $card->duration,
            ]);

        return ApiResponse::success([
            'id' => $series->id,
            'name' => $series->name,
            'description' => $series->description,
            'thumbnail' => $series->thumbnail_path,
            'is_base' => $series->is_base,
            'cards' => $cards,
        ]);
    }
}
