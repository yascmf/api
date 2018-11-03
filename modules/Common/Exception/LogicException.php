<?php

namespace Modules\Common\Exception;

class LogicException extends \Exception
{
    protected $data;

    public function __construct(int $code, $message = null, $previous = null, $data = [])
    {
        $this->data = $data;
        if (!empty($message) && is_string($message)) {
            parent::__construct($message, $code, $previous);
        } else {
            $msg = isset(self::$errorMessages[$code]) ? self::$errorMessages[$code] : 'error code:'.$code;
            parent::__construct($msg, $code, $previous);
        }
    }

    public function getData() {
        return $this->data;
    }


    // 20000 - 39999 区间异常code,仅供 `公共` 使用,其他模块请勿占用 START
    /*20000 - 29999 用于存放非阻塞性、可读的、提示性或辅助性的错误及其文案*/
    const COMMON_SUCCESS                      = 20000;
    const COMMON_PARAMS_MISSING               = 20400;
    const COMMON_VALIDATION_FAIL              = 20500;
    /*30000 - 39999 用于存放阻塞性、可写的、业务中断型的异常错误*/
    const COMMON_FAIL                         = 30000;
    const COMMON_DB_SAVE_FAIL                 = 30100;
    const COMMON_RECORD_NOT_FOUND             = 30400;
    // 20000 - 39999 区间异常code,仅供 `公共` 使用,其他模块请勿占用 END


    // 40000 - 40999 区间异常code,仅供 `用户` 模块使用,其他模块请勿占用 START
    const USER_EXCEPTION               = 40000;
    const USER_HAVE_BEEN_LOCKED        = 40001;
    const USER_LOGIN_FAIL              = 40002;
    const USER_NEED_LOGIN              = 40003;
    const USER_NOT_FOUND               = 40004;
    // 40000 - 40999 区间异常code,仅供 `用户` 模块使用,其他模块请勿占用 END

    public static $errorMessages = [

        // COMMON 20000 - 39999 START
        self::COMMON_SUCCESS                      => 'ok',
        self::COMMON_PARAMS_MISSING               => 'param missing',
        self::COMMON_VALIDATION_FAIL              => 'validation failure',  // 统一存放数据校验型的错误
        self::COMMON_FAIL                         => 'fail',
        self::COMMON_DB_SAVE_FAIL                 => 'database save failure',
        self::COMMON_RECORD_NOT_FOUND             => 'database record not found',
        // COMMON 20000 - 39999 END


        // User 40000 - 40999 START
        self::USER_EXCEPTION          => 'User exception',
        self::USER_HAVE_BEEN_LOCKED   => 'This user have been locked by superAdmin',
        self::USER_LOGIN_FAIL         => 'Fail to login, check your username or password',
        self::USER_NEED_LOGIN         => 'Access token missing or expired, need to login',
        self::USER_NOT_FOUND          => 'User not found, maybe deleted by superAdmin',
        // User 40000 - 40999 END

    ];

}
