<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Modules\Common\Exception\LogicException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class UploadController extends BaseController
{
    /**
     * 上传文件接口
     *
     * @param Request $request
     * @return array
     * @throws LogicException
     */
    public function postFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data = $request->all();
            $rules = [
                'file'    => 'mimes:jpeg,png,gif|max:5120',
            ];
            $messages = [
                'file.required' => '必须传入文件',
                'file.mimes'    => '文件类型不允许,请上传常规的图片(jpg、png、gif)文件',
                'file.max'      => '文件过大,文件大小不得超出5MB',
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->passes()) {
                $realPath = $file->getRealPath();
                $hash = md5_file($realPath);
                $savePath = storage_path('qiniu');
                is_dir($savePath) || mkdir($savePath);  //如果不存在则创建目录
                $name = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $oFile = $hash.'.'.$ext;
                $fullFilename = '/'.$oFile;  //原始完整路径
                if ($file->isValid()) {
                    $uploadSuccess = $file->move($savePath, $oFile);  //移动文件
                    $oFilePath = $savePath.'/'.$oFile;
                    return [
                        'url' => $fullFilename
                    ];
                } else {
                    throw new LogicException(LogicException::COMMON_VALIDATION_FAIL, '文件校验失败');
                }
            } else {
                throw new LogicException(LogicException::COMMON_VALIDATION_FAIL, $validator->messages()->first());
            }

        } else {
            throw new LogicException(LogicException::COMMON_PARAMS_MISSING, '缺少待上传的文件');
        }
    }
}