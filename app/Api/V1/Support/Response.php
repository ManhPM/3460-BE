<?php

namespace App\Api\V1\Support;


use Illuminate\Http\JsonResponse;

trait Response
{
    protected static array $EMPTY_ARRAY = [];
    protected function jsonResponseSuccess(mixed $data, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message ?: __('success'),
            'data' => $data
        ], $status);
    }

    protected function jsonResponseSuccessOnlyData(mixed $data): JsonResponse
    {
        return response()->json(
            $data,
            200
        );
    }

    protected function jsonResponseError(string $message = '', int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message ?: __('fail'),
            'data' => null
        ], $status);
    }
}
