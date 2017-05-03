<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/11/22
 * Time: 14:12
 */
namespace common;

use ZPHP\ZPHP,
    ZPHP\Core\Config;

class ILog
{
    public static function info($data)
    {
        $logPath = dirname(__DIR__) . '/../log/'.date("Y-m-d", time()).'.log';
        $dataOfWrite = '';
        if (is_string($data)) {
            $dataOfWrite = $data;
        }
        if (is_array($data)) {
            $dataOfWrite = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        $dataOfWrite = date('Y-m-d H:i:s', time()) . ' : ' . $dataOfWrite . PHP_EOL;
        file_put_contents($logPath, $dataOfWrite, FILE_APPEND | LOCK_EX);
    }

    public static function log($data)
    {
        if(Config::getField('project', 'is_log', false)){
            $logPath = dirname(__DIR__) . '/../log/'.date("Y-m-d", time()).'.log';
            $dataOfWrite = '';
            if (is_string($data)) {
                $dataOfWrite = $data;
            }
            if (is_array($data)) {
                $dataOfWrite = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
            $dataOfWrite = date('Y-m-d H:i:s', time()) . ' : ' . $dataOfWrite . PHP_EOL;
            file_put_contents($logPath, $dataOfWrite, FILE_APPEND | LOCK_EX);
        }
    }

}