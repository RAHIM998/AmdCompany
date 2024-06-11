<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function responseJson($data, $message, $status_code = 200)
    {
        return response()->json([
            'status' => $status_code < 300,
            'message' => $message,
            'data' => $data,
        ], $status_code);
    }


}
