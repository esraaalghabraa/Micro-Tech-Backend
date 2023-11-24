<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function success
    ($data = null, $message=null, $statusCode=200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $statusCode);

    }

    public function error
    ($message=null, $statusCode=400)
    {
        return response()->json([
            'data' => null,
            'message' => $message
        ], $statusCode);
    }

}
