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
        $children = $request->user()->children;
        return ApiResponse::success($children);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'birthdate'  => ['nullable', 'date'],
            'avatar'     => ['nullable', 'string', 'max:255'],
            'notes'      => ['nullable', 'string'],
        ]);

        $child = $request->user()->children()->create($validated);

        return ApiResponse::success($child, 201);
    }

    public function show(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        return ApiResponse::success($child);
    }

    public function update(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name'  => ['sometimes', 'string', 'max:100'],
            'birthdate'  => ['nullable', 'date'],
            'avatar'     => ['nullable', 'string', 'max:255'],
            'notes'      => ['nullable', 'string'],
        ]);

        $child->update($validated);

        return ApiResponse::success($child);
    }

    public function destroy(Request $request, Child $child): JsonResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        $child->delete();

        return ApiResponse::success(null, 204);
    }
}
