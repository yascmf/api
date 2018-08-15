<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Routing\Controller;
// use Illuminate\Http\Request;
use Modules\Exception\ApiException;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    /**
     * api response (normal success)
     * 
     * @param  array   $data
     * @param  string  $msg
     * @param  integer $code Using 200 when success
     * @return Response json
     */
    protected function api($data = [], $msg = 'OK', $code = 200)
    {
        $json = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        return response()->json($json);
    }

    /**
     * api response (fail or error)
     * 
     * @param  string  $msg
     * @param  integer $code Using non-2xx in fail or error case
     * @param  array   $data
     * @return Response json
     */
    protected function error($msg = 'fail', $code = 500, $data = [])
    {
        if ($code == 200) {
            $code = 500;
        }
        return $this->api($data, $msg, $code);
    }
}