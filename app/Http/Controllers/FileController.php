<?php

namespace App\Http\Controllers;


use App\Models\MaterialFile;
use Auth;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    /**
     * 显示文件
     *
     * @param string $uuid 文件 uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $file = DB::table('files')->find($uuid);
        if ($file) {
            return redirect(url($file->path));
        }
        return abort(404);
    }
}