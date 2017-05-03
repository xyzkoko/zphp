<?php
use ZPHP\ZPHP;
use \ZPHP\Socket\Adapter\Swoole;

$config = array(
    'server_mode' => 'Socket',
    'project_name' => 'zphp',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'socket' => array(
        'host' => '0.0.0.0',                          //socket 监听ip
        'port' => 8992,                             //socket 监听端口
        'adapter' => 'Swoole',                          //socket 驱动模块
        'server_type' => Swoole::TYPE_TCP,              //socket 业务模型 tcp/udp/http/websocket
        'daemonize' => 0,                             //是否开启守护进程
        'client_class' => 'socket\\Swoole',            //socket 回调类
        'protocol' => 'Json',                         //socket通信数据协议
        'work_mode' => 3,                             //工作模式：1：单进程单线程 2：多线程 3： 多进程
        'worker_num' => 5,                                 //工作进程数
        'max_request' => 1000,                            //单个进程最大处理请求数
        'debug_mode' => 1,                                  //打开调试模式
    ),
    'project' => array(
        'name' => 'zphp_base',                 //项目名称。(会做为前缀，隔离不同的项目)
        'error_handler' => 'common\\IErrorHandler::errorHandler', //错误回调函数
        'exception_handler' => 'common\\IExceptionHandler::exceptionHandler'//异常回调函数
    )
);
$publicConfig = array('pdo.php');
foreach($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'common'. DS . $file;
    $config += include "{$file}";
}
return $config;
