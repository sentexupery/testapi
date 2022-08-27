<?php

namespace App\Http\Controllers\Api\Helpers;
use Illuminate\Http\JsonResponse;

trait Role
{
    protected function isAdmin($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('admin');
        }
        return false;
    }
    protected function isOperator($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('operator');
        }
        return false;
    }
    protected function isDeliveryServices($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('delivery_service');
        }
        return false;
    }
    protected function onSuccess($data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    protected function onError(int $code, string $message = ''): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
        ], $code);
    }
}
