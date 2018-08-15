<?php

namespace Modules\Common\Exception;

/**
 * API 异常类
 *
 * @author raoyc <raoyc2009@gmail.com>
 */
class ApiException extends \Exception
{
    public static function getClass() 
    {
        return __CLASS__;
    }
}
