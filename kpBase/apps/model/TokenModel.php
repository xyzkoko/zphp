<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/10/20
 * Time: 16:22
 */
namespace model;

use common\IException;
use common\medoo;

class TokenModel
{
    public function createToken($params, $ip)
    {
        $tokenDao = new medoo();
        $token = md5(uniqid($params['id'] . $params['shop_id']));
        $refresh_token = md5(uniqid($params['id'] . mt_rand(10000, 99999)));
        if ($token && $refresh_token) {
            $time = time();
            $data = [
                'from_type' => 1,
                'value' => $token,
                'refresh_token' => $refresh_token,
                'from_id' => $params['id'],
                'shop_id' => $params['shop_id'],
                'expires_in' => 7200,
                'end_time' => date('Y-m-d H:i:s', $time + 7200),
                'ip' => $ip,
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $result = $tokenDao->insert('pos_token', $data);
            if ($result) {
                $data = [
                    'access_token' => $token,
                    'refresh_token' => $refresh_token,
                    'expires_in' => '7200'
                ];
                return $data;
            } else {
                throw new IException('10004');
            }
        } else {
            throw new IException('10004');
        }
    }

}