<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    public static function error(string $message, int $status, array $errors = []): JsonResponse
    {
        $body = ['message' => $message];
        if (! empty($errors)) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status);
    }
}
