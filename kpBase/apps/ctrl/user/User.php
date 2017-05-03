<?php
namespace ctrl\user;

use model\TokenModel;
use model\UserModel;
use ZPHP\Controller\IController,
    ZPHP\Core\Config,
    ZPHP\View;
use ZPHP\Protocol\Request;
use ZPHP\Db\Pdo;
use common\BaseCtrl;

class User extends BaseCtrl
{
    public function Login()
    {
        $params = Request::getParams();
        $userModel = new UserModel();
        $userResult = $userModel->login($params['data']);
        $tokenModel = new TokenModel();
        $tokenResult = $tokenModel->createToken($userResult, $params['ip']);
        $data['data'] = $userResult;
        $data['token'] = $tokenResult;
        $result = [
            'errcode' => '0',
            'errmsg' => 'ok',
            'data' => $data
        ];
        return $result;
    }

}

