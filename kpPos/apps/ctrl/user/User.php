<?php
namespace ctrl\user;



use common\BaseCtrl;
use common\IClient;
use rule\AllRule;
use ZPHP\Protocol\Request;

class User extends BaseCtrl
{
    /**
     * 登陆
     */
    public function Login()
    {
        $data = Request::getParams();
        $UserRule = new AllRule();
        $UserRule->loginRule($data['post']);
        $back = IClient::tcp('user\User','Login',$data['post'], $data['server']['remote_addr']);
        return json_decode($back, true);
    }

}

