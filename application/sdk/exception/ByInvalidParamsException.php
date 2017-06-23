<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-06-14
 * Time: 10:54
 */

namespace app\sdk\exception;


use app\sdk\constants\ByRetCode;
use Throwable;

class ByInvalidParamException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, ByRetCode::Api_Invalid_Parameter, $previous);
    }
}