<?php

if (!function_exists('api_response')) {
    function api_response(int $code, string $message, array $data = [])
    {
        return response()->json([
            'header' => [
                'status' => $code,
                'message' => $message
            ],
            'body' => $data
        ]);
    }
}
