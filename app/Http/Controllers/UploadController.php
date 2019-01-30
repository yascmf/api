<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Common\Exception\LogicException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class UploadController extends Controller
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
                // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
                // 文件夹切割能让查找效率更高。
                $folder_name = "uploads/images/posts/" . date("Ym", time()) . '/'.date("d", time()).'/';
                // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
                $upload_path = base_path('public') . '/' . $folder_name;
                /*
                is_dir($upload_path) || mkdir($upload_path);  // 如果不存在则创建目录
                */
                // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
                $real_path = $file->getRealPath();
                $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
                $mime = strtolower($file->getMimeType());
                $mimeArray = explode('/', $mime);
                $type = isset($mimeArray[0]) ? $mimeArray[0] : 'unknown';

                // 如果上传的不是图片将终止操作
                if (! in_array($extension, ['png', 'gif', 'jpeg', 'jpg']) || ($type !== 'image')) {
                    throw new LogicException(LogicException::COMMON_VALIDATION_FAIL, '不允许上传该类型的文件');
                }
                $uuid = Uuid::uuid4()->toString();
                $hash = md5_file($real_path);
                $size = $file->getSize();

                // 使用md5值作为文件名，避免重复上传相同图片
                $filename = $hash.'.'.$extension;
                $local_path = $remote_path = '/'.$folder_name.''.$filename;

                list($width, $height, $iType, $attr) = getimagesize($real_path);
                unset($iType, $attr);

                // 将图片移动到我们的目标存储路径中
                if ($file->isValid()) {
                    $file->move($upload_path, $filename);
                    $meta = [
                        'width' => isset($width) ?: 0,
                        'height' => isset($height) ?: 0,
                    ];
                    $time = date('Y-m-d H:i:s');
                    DB::table('files')->insert([
                        'id' => $uuid,
                        'user_id' => Auth::guest() ? '0' : Auth::user()->id,
                        'name' => $filename,
                        'type' => $type,
                        'extension' => $extension,
                        'path' => $local_path,
                        'mime' => $mime,
                        'original_name' => $file->getClientOriginalName() ?: str_random(6),
                        'original_extension' => $extension,
                        'original_mime' => $mime,
                        'size' => $size,
                        'meta' => json_encode($meta),
                        'md5_hash' => $hash,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ]);
                    return response()->json([
                        'files' => [
                            'file' => url('/file/'.$uuid),
                        ],
                    ]);
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