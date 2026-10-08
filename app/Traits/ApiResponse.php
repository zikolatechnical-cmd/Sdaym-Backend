<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public static function response($status = 200, $data = null, $type = 'success', $msg = ''): JsonResponse
    {
        $response = ['data' => $data, 'message' => $msg];

        if ($type == 'success') {

            $response['data'] = $data;
            // $response['additional'] = $data?->additional ?? [];

            $response['message'] = ($msg == '') ? __('request done successfully') : $msg;
        }

        return response()->json($response, $status, []);
    }

    public static function warning($message = '', $data = null): JsonResponse
    {
        return self::response(400, $data, 'warning', $message);
    }

    public static function notFound($message = '', $data = null): JsonResponse
    {
        return self::response(404, $data, 'not found', $message);
    }

    public static function validation($message, $data = null): JsonResponse
    {
        return self::response(422, $data, 'warning', null, $message, '');
    }

    public static function success($message = '', $data = null): JsonResponse
    {
        return self::response(200, $data, 'success', $message);
    }

    public static function unAuth($message = 'Unauthenticated', $code = 401, $data = null): JsonResponse
    {
        return self::response($code, $data, 'fails', $message);
    }

    public static function forbidden($message = 'Forbidden', $code = 403, $data = null): JsonResponse
    {
        return self::response($code, $data, 'fails', $message);
    }

    public static function authFail($code = 400, $data = null): JsonResponse
    {
        return self::response($code, $data, 'unAuth', null, trans('auth.failed'));
    }

    public static function fails($error = '', $data = null): JsonResponse
    {
        $errorMessage = ($error == '') ? trans('api.server-internal-error') : $error;

        return self::response(500, $data, 'fails', null, '', $errorMessage);
    }
}
