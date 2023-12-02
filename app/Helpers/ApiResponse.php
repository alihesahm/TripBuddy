<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;

/**
 *  return success response
 */
function sendSuccessResponse(string $message = 'OK', array|Arrayable|JsonSerializable $data = null, int $status_code = 200): JsonResponse
{
    $response = [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'status_code' => $status_code,
    ];

    return response()->json(
        $response,
        $status_code
    );
}

/**
 * return failure response
 */
function sendFailedResponse(string $message = 'Error', array|Arrayable|JsonSerializable $data = null, int $status_code = 404): JsonResponse
{
    $response = [
        'success' => false,
        'message' => $message,
        'data' => $data,
        'status_code' => $status_code,
    ];

    return response()->json(
        $response,
        $status_code
    );
}
