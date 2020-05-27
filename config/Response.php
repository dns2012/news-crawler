<?php 

namespace Config;

class Response
{
    public static function success($data = [], $code)
    {
        header('Content-type: application/json', true, $code);
        echo json_encode([
            'status' => true,
            'data' => $data
        ]);
    }

    public static function failure($message = null, $code)
    {
        header('Content-type: application/json', true, $code);
        echo json_encode([
            'status' => false,
            'message' => $message
        ]);
    }
}