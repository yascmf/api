<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
//use Illuminate\Http\JsonResponse;
//use Modules\Common\Exception\LogicException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
//        // 404  Not Found error
//        if ($exception instanceof NotFoundHttpException) {
//            $json = [
//                'message' => '404 Not Found',
//                'errors' => 'App API NOT FOUND',
//                'code' => LogicException::COMMON_NOT_FOUND,
//                'status_code' => 404,
//            ];
//            return new JsonResponse($json, '404');
//        }
//        // 405 Method Not Allowed error
//        if ($exception instanceof MethodNotAllowedHttpException /*&& !$request->isMethod('OPTIONS')*/) {
//            $json = [
//                'message' => '405 Method Not Allowed',
//                'errors' => 'App API Method Not Allowed',
//                'code' => LogicException::COMMON_METHOD_NOT_ALLOWED,
//                'status_code' => 405,
//            ];
//            return new JsonResponse($json, '405');
//        }

        return parent::render($request, $exception);
    }
}
