<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/10/25
 * Time: 16:00
 */
namespace common;

use ZPHP\Core\Config;
use ZPHP\Socket\Client;

class IClient
{
    public static function tcp($ctrl, $meth, $data)
    {
        if (!is_array($data)) {
            return false;
        }
        $sendData = [
            'ctrl' => $ctrl,
            'meth' => $meth,
            'data' => $data
        ];
        $sendData = json_encode($sendData);

        $host = Config::getField('tcp', 'host');
        $port = Config::getField('tcp', 'port');
        $client = new Client($host, $port);
        $client->send($sendData);
        $result = $client->read();
        if (!is_null(json_decode($result, true))) {
            return json_decode($result, true);
        }
        return $result;
    }
}