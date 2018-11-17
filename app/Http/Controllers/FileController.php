<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class FileController extends Controller
{
    /**
     * 文件的uuid
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function show(Request $request, $uuid)
    {
        //127.0.0.1:8080/files/24cef44f-2e57-4899-8509-7d159d6fb813?disk=local
        $usingCdn = env('USING_CDN', false);
        $disk = $request->query('disk', 'remote');
        $width = $request->query('w', null);
        $height = $request->query('h', null);
        $file = DB::table('files')->find($uuid);
        if ($file) {
            if ($file->type === 'image') {
                // Set source filesystem
                $source = new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Adapter\Local(base_path('public'))
                );

                // Set cache filesystem
                $cache = new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Adapter\Local(storage_path('app/images-cache'))
                );

                // Set watermarks filesystem
                $watermarks = new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Adapter\Local(storage_path('app/images-watermarks'))
                );

                // Set image manager
                $imageManager = new \Intervention\Image\ImageManager([
                    'driver' => 'gd',
                ]);

                // Set manipulators
                $manipulators = [
                    new \League\Glide\Manipulators\Crop(),
                    new \League\Glide\Manipulators\Size(2000*2000),
                    new \League\Glide\Manipulators\Watermark($watermarks),
                    new \League\Glide\Manipulators\Filter(),
                    new \League\Glide\Manipulators\Blur(),
                    new \League\Glide\Manipulators\Pixelate(),
                    new \League\Glide\Manipulators\Encode(),
                ];

                // Set API
                $api = new \League\Glide\Api\Api($imageManager, $manipulators);

                // Setup Glide server
                $server = new \League\Glide\Server(
                    $source,
                    $cache,
                    $api
                );
                // Set response factory
                $server->setResponseFactory(new \League\Glide\Responses\LaravelResponseFactory($request));
            }
            $query = [];
            $qiniuQuery = '';
            if ($width) {
                $query['w'] = $width;
                $qiniuQuery = '?imageView2/2/w/'.$width;
            }
            if ($height) {
                $query['h'] = $height;
                $qiniuQuery = '?imageView2/2/h/'.$height;
            }
            if ($width && $height) {
                $query['fit'] = 'crop';
                $qiniuQuery = '?imageView2/1/w/'.$width.'/h'.$height;
            }
            if ($request->query('blur') || $request->query('filt') || $request->query('pixel')) {
                $effects = [];
                if ($request->query('blur')) {
                    $effects['blur'] = 10;
                }
                if ($request->query('filt')) {
                    $effects['filt'] = 'sepia';
                }
                if ($request->query('pixel')) {
                    $effects['pixel'] = 3;
                }
                $query = array_merge($query, $effects);
            }
            if ($disk === 'local') {
                if ($file->type === 'image') {
                    return $server->getImageResponse($file->local_path, $query);
                } else {
                    return redirect(url($file->local_path));
                }
            }
            if ($usingCdn) {
                $filePath = $file->remote_path;
                if ($file->type === 'image') {
                    $filePath = $file->remote_path.$qiniuQuery;
                }
                $url = env('QINIU_DOMAIN', '').$filePath;
                return redirect($url, 301);
            } else {
                if ($file->type === 'image') {
                    return $server->outputImage($file->local_path, $query);
                } else {
                    return redirect(url($file->local_path));
                }
            }
        }
        return abort(404);
    }
}