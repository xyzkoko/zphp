<?php

namespace socket;

use ZPHP\Common\Formater;
use ZPHP\Protocol\Request;
use ZPHP\Protocol\Response;
use ZPHP\Socket\Callback\SwooleHttp as ZSwooleHttp;
use ZPHP\Socket\IClient;
use ZPHP\Core\Route as ZRoute;

class SwooleHttp extends ZSwooleHttp
{
    public function onRequest($request, $response)
    {
        $param = [];
        $param['header'] = $request->header;
        $param['server'] = $request->server;
        //get请求数据
        if(!empty($request->get)) {
            $param['get'] = $request->get;
        }

        /**
         * 标准表单post数据
         */
        if(!empty($request->post)) {
            $param['form_post'] = $request->post;
        }
        //标准表单文件数据
        if(!empty($request->files)){
            $param['files'] = $request->files;
        }
        /**
         * 非表单post数据
         */
        if(!empty($request->rawContent())){
            if(!is_null(json_decode($request->rawContent(), true))){
                $param['post'] = json_decode($request->rawContent(), true);
            }else {
                $param['post'] = $request->rawContent();
            }
        }

        Request::parse($param);
        try {
            $result = ZRoute::route();
        } catch (\Exception $e) {
            $model = Formater::exception($e);
            $model['_view_mode'] = 'Json';
            $result = Response::display($model);
        }
        $response->end($result);
    }

    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);
    }

}
