<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 * Json view
 */


namespace ZPHP\View\Adapter;

use ZPHP\Protocol\Request;
use ZPHP\Protocol\Response;
use ZPHP\View\Base,
    ZPHP\Core\Config;

class Json extends Base
{
    public function display()
    {
        /**
         * 抛出异常时只输出message和code
         */
        $data = $this->model;
        $isArray = false;
        if ($data['className'] == 'Exception') {
            $data = ['errmsg' => $data['message'], 'errcode' => $data['code']];
            $data = \json_encode($data, JSON_UNESCAPED_UNICODE);
            $isArray = true;
        } else if (is_array($data)) {
            $data = \json_encode($this->model, JSON_UNESCAPED_UNICODE);
            $isArray = true;
        }
        if (Request::isHttp()) {
            $params = Request::getParams();
            $key = Config::getField('project', 'jsonp', 'jsoncallback');
            if (isset($params[$key])) {
                Response::header("Content-Type", 'application/x-javascript; charset=utf-8');
                $data = $params[$key] . '(' . $data . ')';
            } else if ($isArray) {
                Response::header("Content-Type", "application/json; charset=utf-8");
            }
        }
        if (Request::isLongServer()) {
            return $data;
        }
        echo $data;
        return null;

    }


}
