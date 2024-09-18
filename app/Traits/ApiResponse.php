<?php

namespace App\Traits;

trait ApiResponse
{
    public function responseSuccess($data = [], $message = 'Success', $code = 200)
    {
        return response()->json(
            [
            'status' => 'success',
            'message' => $message,
            'data' => $data
            ],
            $code
        );
    }

    public function responseFail($message = 'Action fail', $code = 400)
    {
        return response()->json(
            [
            'status' => 'fail',
            'message' => $message,
            ],
            $code
        );
    }
}
