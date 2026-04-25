<?php

namespace App\Http\Responses;

class ApiResponse
{
    public static function success(mixed $data = null, int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status);
    }

    public static function error(string $message, int $status, array $errors = []): \Illuminate\Http\JsonResponse
    {
        $body = ['message' => $message];
        if (!empty($errors)) {
            $body['errors'] = $errors;
        }
        return response()->json($body, $status);
    }
}
