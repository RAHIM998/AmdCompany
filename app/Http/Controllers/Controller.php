<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

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

    public function imageToBlob($images){
        $file= fopen($images->getRealPath(), 'rb');
        $content = stream_get_contents($file);
        fclose($file);
        return base64_encode($content);
    }

    public function searchproduct(ModelNotFoundException $e)
    {
        return $this->responseJson(null, 'Ressource non trouvé !!', 404);
    }

}
