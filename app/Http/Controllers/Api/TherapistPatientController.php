<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TherapistPatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $patients = $request->user()->activePatients()->get();
        return ApiResponse::success($patients);
    }

    public function show(Request $request, Child $child): JsonResponse
    {
        $isPatient = $request->user()->activePatients()
            ->where('children.id', $child->id)
            ->exists();

        if (!$isPatient) {
            return ApiResponse::error('Accès refusé.', 403);
        }

        return ApiResponse::success($child);
    }
}
