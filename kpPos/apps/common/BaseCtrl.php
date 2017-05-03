<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/10/24
 * Time: 18:24
 */
namespace common;

use common\IException;
use ZPHP\Controller\IController,
    ZPHP\Core\Config,
    ZPHP\View;
use ZPHP\Protocol\Request;
use common\Curl;

class BaseCtrl implements IController
{
    /**
     * 业务逻辑开始前执行
     */
    function _before()
    {
        //记录请求日志
        $action = $this->getAction();
        $this->log($action);
        //验证海豚云用户身份信息
        if(strpos(Request::getPathInfo(), 'small') !== false &&
            strpos(Request::getPathInfo(), 'small/code') === false &&
            strpos(Request::getPathInfo(), 'customer/code') === false &&
            strpos(Request::getPathInfo(), 'small/manual') === false
        ){
            $data = Request::getParams();
            return Sign::checkCloudKey($data);
        }
        if(strpos(Request::getPathInfo(), 'small') !== false || Request::getPathInfo() == '/v1'){
            return true;
        }

        //排除不需要验证签名的boss端接口
        if (strpos($action, 'Report') !== false) {
            return true;
        }


        switch ($action) {
            case 'User-Login':
                return true;
                break;
        }

        $data = Request::getParams();
        ILog::info(json_encode($data['post']));
        Sign::signOfRequest($data, $action);
        return true;
    }

    /**
     * 业务逻辑结束后执行
     */
    function _after()
    {

    }

    /**
     * 获取请求方法名
     */
    function getAction()
    {
        $ctrl = Request::getCtrl();
        $ctrl = str_replace('\\', '', strstr($ctrl, '\\'));
        $method = Request::getMethod();
        return $ctrl . '-' . $method;
    }

    /**
     * 记录请求日志
     */
    function log($action)
    {
        $data = Request::getParams();
        $str = 'action : ' . $action . '      ' . json_encode($data['post']);
        ILog::log($str);
    }

    /**
     * @param $type
     * @param string $url
     * @param $data
     * @return string
     * @throws \common\IException   curl封装
     */
    public function curl($type, $url = '', $data)
    {
        if (!$url) {
            throw new IException('10010');
        }

        if (is_array($data)) {
            $data = json_encode($data);
        }

        $response = '';
        $curl = new Curl();
        switch ($type) {
            case 'post':
                $response = $curl->setOption(CURLOPT_POSTFIELDS, $data)
                    ->setOption(CURLOPT_RETURNTRANSFER, true)
                    ->post($url);
                break;
            case 'get':
                $response = $curl->setOption(CURLOPT_RETURNTRANSFER, true)
                    ->get($url);
                break;
            default:
                break;
        }

        // 处理结果
        return $response;
    }
}