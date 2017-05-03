<?php
/**
 * Created by PhpStorm.
 * Author: kx
 * Date: 2016/12/9
 * Time: 8:59
 */
namespace common;


class IErrorHandler
{
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno == E_ERROR || $errno == E_RECOVERABLE_ERROR || $errno == E_USER_ERROR) {
            $str = '[error] ' . $errfile . '  line  ' . $errline . '  :  ' . $errstr;
            ILog::info($str);
            $back_str = '[error]服务错误';
            throw new IException($back_str);
        }
        return true;
    }
}