<?php
/**
 * Created by PhpStorm.
 * Author: kx
 * Date: 2016/12/9
 * Time: 11:22
 */
namespace common;

class IExceptionHandler
{
    public static function exceptionHandler($exception)
    {
        $data = \ZPHP\Common\Formater::exception($exception);
        if (strpos($data['message'], '[error]') === false) {
            $str = '[exception] ' . $data['file'] . '  line  ' . $data['line'] . '  :  ' . $data['code'] . '  ' . $data['message'];
            ILog::log($str);
        }else {
            $data['message'] = str_replace('[error]','',$data['message']);
        }
        return \ZPHP\Protocol\Response::display($data);
    }
}