<?php
namespace ctrl\main;

use common\IException;
use ZPHP\Controller\IController,
    ZPHP\Core\Config as ZConfig,
    ZPHP\View;
use ZPHP\Protocol\Request;
use ZPHP\Db\Pdo as ZPdo;

class main implements IController
{
    public function _before()
    {
        return true;
    }

    public function _after()
    {
        //
    }

    public function main()
    {
        throw new IException('url error');
    }
}

