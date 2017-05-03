<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/10/24
 * Time: 18:24
 */
namespace common;

use ZPHP\Controller\IController,
    ZPHP\Core\Config,
    ZPHP\View;
use ZPHP\Protocol\Request;


class BaseCtrl implements IController{
    /**
     * 业务逻辑开始前执行
     */
    function _before(){
        return true;
    }

    /**
     * 业务逻辑结束后执行
     */
    function _after(){

    }
}