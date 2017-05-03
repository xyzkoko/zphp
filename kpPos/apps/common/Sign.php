<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/11/21
 * Time: 16:12
 */
namespace common;


class Sign
{
    public static function signOfRequest($params, $action)
    {
        if (!$params['get']['timestamp']) {
            throw new IException('10002');
        }
        if (!$params['post']['login_id']) {
            throw new IException('10003');
        }
        if (!$params['get']['access_token']) {
            throw new IException('10004');
        }
        if (!$params['get']['sign']) {
            throw new IException('10005');
        }
        self::timeStamp($params, $action);
        self::token($params);
        self::checkSign($params);
        return true;
    }

    /**
     * 时间戳校验
     */
    private function timeStamp($params, $action)
    {

        $data = [
            'login_id' => $params['post']['login_id'],
            'timestamp' => $params['get']['timestamp'],
            'action' => $action
        ];

        $tcpData = ['ctrl' => 'shop/ShopStaff', 'meth' => 'GetTimeStamp', 'data' => $data];
        $tcpData = json_encode($tcpData);
        $client = new IClient();
        $client->send($tcpData);
        $result = $client->read();
        $result = json_decode($result, true);
        if ($result['data']['result'] == false) {
            throw new IException('10001');
        }
        return true;
    }

    /**
     * token验证
     */
    private function token($params)
    {
        $data = [
            'token' => $params['get']['access_token']
        ];
        $tcpData = ['ctrl' => 'shop/ShopStaff', 'meth' => 'GetToken', 'data' => $data];
        $tcpData = json_encode($tcpData);
        $client = new IClient();
        $client->send($tcpData);
        $result = $client->read();
        $result = json_decode($result, true);
        if ($result['data']['result'] == false) {
            throw new IException('10006');
        }
        return true;
    }

    /**
     * 签名校验
     */
    private function checkSign($params)
    {
        $key = md5($params['get']['access_token'] . $params['get']['timestamp']);
        $post = json_encode($params['post'], JSON_UNESCAPED_UNICODE);
        $string = $key . $post . $params['get']['timestamp'] . $key;
        $string = md5($string);
        $string = strtoupper($string);

        if ($params['get']['sign'] != $string) {
            throw new IException('10007');
        }
        return true;

    }

    /**
     * 验证海豚云身份信息
     */
    public static function checkCloudKey($params)
    {
        $session_key = $params['get']['session_key'];
        if(!$session_key){
            throw new IException('10011');
        }
        $redis = new Redis();
        if(!$redis->get($session_key)){
            throw new IException('10012');
        }
        return true;
    }
}