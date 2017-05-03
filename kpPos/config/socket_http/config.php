<?php

use ZPHP\ZPHP;
use ZPHP\Socket\Adapter\Swoole;

$config = array(
    'server_mode' => 'Socket',
    'project_name' => 'zphp',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'debug_mode' => 1,
    'log_path' => 'socket',
    'socket' => array(
        'host' => '0.0.0.0',                          //socket 监听ip
        'port' => 8991,                             //socket 监听端口
        'adapter' => 'Swoole',                          //socket 驱动模块
        'server_type' => Swoole::TYPE_HTTP,              //socket 业务模型 tcp/udp/http/websocket
        'protocol' => 'Http',                         //socket通信数据协议
        'daemonize' => 0,                             //是否开启守护进程
        'client_class' => 'socket\\SwooleHttp',            //socket 回调类
        'work_mode' => 3,                             //工作模式：1：单进程单线程 2：多线程 3： 多进程
        'worker_num' => 50,                                 //工作进程数
        'max_request' => 1000,                            //单个进程最大处理请求数
        'debug_mode' => 1,                                  //打开调试模式
    ),
    'session' => array(
        'adapter' => 'Redis',
        'name' => 'sso',
        'pconnect' => true,
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 0,
        'session_name' => 'ZPHP_SID',
        'cache_expire' => 20160,
        'path' => '/',
        'serure' => false,
        'httponly' => true,
    ),
    'project' => array(
        'name' => 'zphp',                 //项目名称。(会做为前缀，隔离不同的项目)
        'view_mode' => 'Json',        //view模式
        'ctrl_name' => 'a',                //ctrl参数名
        'method_name' => 'm',                //method参数名    http://host/?{action_name}=main\main&{method_name}=main
//        'log_path'=>'socket',
//        'debug_mode' => 1,
        'error_handler' => 'common\\IErrorHandler::errorHandler', //错误回调函数
        'exception_handler' => 'common\\IExceptionHandler::exceptionHandler',//异常回调函数
        'is_log' => true           //是否开启系统日志
    ),
    'cache'=>array(               //缓存
        'adapter'=>'Redis',
        '_prefix'=>'public',
        'name'=>'cache',
        'pconnect'=>false,
        'host'=>'192.168.10.193',
        'port'=>6379,
        'timeout'=>5
    ),
);

$publicConfig = array('route.php', 'tcp.php');
foreach ($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'common' . DS . $file;
    $config += include "{$file}";
}
return $config;
