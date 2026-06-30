<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $children = auth()->user()
            ->children()
            ->with(['activeTherapists', 'subscription', 'series'])
            ->get()
            ->map(fn($child) => [
                'id' => $child->id,
                'first_name' => $child->first_name,
                'last_name' => $child->last_name,
                'birthdate' => $child->birthdate,
                'avatar' => $child->avatar,
                'has_therapist' => $child->activeTherapists->isNotEmpty(),
                'last_series' => ($last = $child->series
                    ->whereNotNull('pivot.last_played_at')
                    ->sortByDesc('pivot.last_played_at')
                    ->first()
                ) ? [
                    'name' => $last->name,
                    'played_at' => $last->pivot->last_played_at
                ] : null,
                'subscription_status' => $child->subscription?->status,
            ]);

        return response()->json($children);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birthdate' => ['nullable', 'date'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $child = $request->user()->children()->create($validated);

        return ApiResponse::success($child, 201);
    }

    public function show(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $child->load(['activeTherapists', 'subscription']);

        return response()->json([
            'id'                  => $child->id,
            'first_name'          => $child->first_name,
            'last_name'           => $child->last_name,
            'birthdate'           => $child->birthdate,
            'avatar'              => $child->avatar,
            'notes'               => $child->notes,
            'has_therapist'       => $child->activeTherapists->isNotEmpty(),
            'therapists'          => $child->activeTherapists->map(fn($t) => [
                'id'   => $t->id,
                'name' => $t->first_name . ' ' . $t->last_name,
            ]),
            'subscription'        => $child->subscription ? [
                'status'      => $child->subscription->status,
                'next_payment'=> $child->subscription->next_payment_at,
            ] : null,
        ]);
    }

    public function update(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'birthdate' => ['nullable', 'date'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $child->update($validated);

        return ApiResponse::success($child);
    }

    public function destroy(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error(__('api.common.access_denied'), 403);
        }

        $child->delete();

        return ApiResponse::success(null, 204);
    }
}
