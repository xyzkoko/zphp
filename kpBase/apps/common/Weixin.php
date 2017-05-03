<?php
/**
 * Created by PhpStorm.
 * Author: kx
 * Date: 2017/2/20
 * Time: 9:59
 */
namespace common;

use ZPHP\ZPHP;
use common\weixinlib\WXBizDataCrypt;

class Weixin extends BaseCtrl
{
    private $template_id = [
        'add' => 'xuu0MsWW-65_z9kDJK0-r82_4FOO9UYmLgoQ7P7FL9s',
        'cancel' => 'vR7DiBoNLOawl6wkr-rzrquucZAsFIfcJQgHZA-0Jes',
        'confirm' => 'oG_RwW5MLGfZ77Kc2VTElMM9l87XIyS-KtsGFlU3FkQ'
    ];

    public function getOpenId($params)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . APPID .
            '&secret=' . SECRET . '&js_code=' . $params['js_code'] . '&grant_type=authorization_code';
        echo date('H:i:s', time()) . '\n';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        curl_close($ch);
        echo date('H:i:s', time()) . '\n';
        $openId = json_decode($result, true);
        if ($openId['errcode']) {
            throw new IException('10015');
        }

        $session_key = md5($openId['openid']);
        $redis = new Redis();
        $redis->set($session_key, json_encode($openId), 7200);

        return [
            'openid' => $openId['openid'],
            'session_key' => $session_key
        ];
    }

    public function getUserInfo($openId)
    {
        $publicToken = $this->getPublicToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $publicToken . '&openid=' .
            $openId . '&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        return $result['unionid'];
    }

    public function getUnionId($session_key, $params)
    {
        $decrypt = new WXBizDataCrypt(APPID, $session_key);
        $userInfo = '';
        $decryptResult = $decrypt->decryptData($params['encrypted_data'], $params['iv'], $userInfo);
        if ($decryptResult != 0) {
            throw new IException($decryptResult);
        }

        return $userInfo['unionId'];
    }

    public function getPublicToken()
    {
        $redis = new Redis();
        $access_token = $redis->get('weixin_public_access_token');
        if ($access_token) {
            $access_token = json_decode($access_token, true);
            return $access_token['access_token'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . PUBLIC_APPID .
                '&secret=' . PUBLIC_SECRET;
            $result = $this->curl('get', $url);
            $result = json_decode($result, true);
            if ($result['errcode']) {
                throw new IException('10016');
            }

            $redis->set('weixin_public_access_token', json_encode($result), 7200);
            return $result['access_token'];
        }

    }

    public function getToken()
    {
        $redis = new Redis();
        $access_token = $redis->get('weixin_access_token');
        if ($access_token) {
            $access_token = json_decode($access_token, true);
            return $access_token['access_token'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . APPID .
                '&secret=' . SECRET;
            $result = $this->curl('get', $url);
            $result = json_decode($result, true);
            if ($result['errcode']) {
                throw new IException('10016');
            }

            $redis->set('weixin_access_token', json_encode($result), 7200);
            return $result['access_token'];
        }

    }

    public function sendModMsg($data, $type)
    {

        $access_token = $this->getPublicToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
        $postData = '';
        if ($type == 'add') {
            $postData = [
                'touser' => $data['public_open_id'],
                'template_id' => $this->template_id[$type],
                'url' => 'kp.seuic.info/wxpay/example/jsapi.php',
//                'url' => URL . '/frontImg/html/message.html?first=' . $data['first'] . '&order_id=' . $data['order_id'] .
//                    '&price=' . $data['total_price'] . '&c_name=' . $data['c_name'] . '&time=' . $data['time'] .
//                    '&phone=' . $data['phone'] . '&last=' . $data['last'] . '&flag=' . $data['flag'],
//                'miniprogram' => [
//                    'appid' => APPID,
//                    'pagepath' => 'pages/splash/splash?flag=' . $data['flag'] . '&order_id=' . $data['order_id']
//                ],
                'data' => [
                    'first' => [
                        'value' => $data['first'],
                        'color' => '#173177'
                    ],
                    'keyword1' => [
                        'value' => $data['order_id'],
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' => $data['total_price'],
                        'color' => '#173177'
                    ],
                    'keyword3' => [
                        'value' => $data['time'],
                        'color' => '#173177'
                    ],
                    'keyword4' => [
                        'value' => $data['c_name'],
                        'color' => '#173177'
                    ],
                    'keyword5' => [
                        'value' => $data['phone'],
                        'color' => '#173177'
                    ],
                    'remark' => [
                        'value' => $data['last'],
                        'color' => '#173177'
                    ]
                ]
            ];
        }
        if ($type == 'cancel') {
            $postData = [
                'touser' => $data['public_open_id'],
                'template_id' => $this->template_id[$type],
                'url' => URL . '/frontImg/html/message.html?first=' . $data['first'] . '&order_id=' . $data['order_id'] .
                    '&price=' . $data['money'] . '&last=' . $data['last'] . '&flag=' . $data['flag'] . '&time=' . $data['time'],
                'miniprogram' => [
                    'appid' => APPID,
                    'pagepath' => 'pages/splash/splash?flag=' . $data['flag'] . '&order_id=' . $data['order_id']
                ],
                'data' => [
                    'first' => [
                        'value' => $data['first'],
                        'color' => '#173177'
                    ],
                    'keyword1' => [
                        'value' => $data['order_id'],
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' => $data['money'],
                        'color' => '#173177'
                    ],
                    'remark' => [
                        'value' => $data['last'],
                        'color' => '#173177'
                    ]
                ]
            ];
        }
        if ($type == 'confirm') {
            $postData = [
                'touser' => $data['public_open_id'],
                'template_id' => $this->template_id[$type],
                'url' => URL . '/frontImg/html/message.html?first=' . $data['first'] . '&order_id=' . $data['order_id'] .
                    '&price=' . $data['money'] . '&last=' . $data['last'] . '&flag=' . $data['flag'] . '&time=' . $data['time'],
                'miniprogram' => [
                    'appid' => APPID,
                    'pagepath' => 'pages/splash/splash?flag=' . $data['flag'] . '&order_id=' . $data['order_id']
                ],
                'data' => [
                    'first' => [
                        'value' => $data['first'],
                        'color' => '#173177'
                    ],
                    'orderno' => [
                        'value' => $data['order_id'],
                        'color' => '#173177'
                    ],
                    'amount' => [
                        'value' => $data['money'],
                        'color' => '#173177'
                    ],
                    'remark' => [
                        'value' => $data['last'],
                        'color' => '#173177'
                    ]
                ]
            ];
        }
        $result = $this->curl('post', $url, $postData);
        $result = json_decode($result, true);
//        if ($result['errcode'] != 0) {
//            throw new IException($result['errmsg']);
//        }
        return true;
    }

    public function sendMsg($data, $type)
    {

        $access_token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $access_token;
        $postData = '';
        if ($type == 'add') {
            $postData = [
                'touser' => $data['weixin'],
                'template_id' => $this->template_id[$type],
                'page' => 'pages/splash/splash?order_id=' . $data['order_id'] . '&flag=' . $data['flag'],
                'form_id' => $data['form_id'],
                'data' => [
                    'keyword1' => [
                        'value' => $data['c_name'],
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' => $data['order_id'],
                        'color' => '#173177'
                    ],
                    'keyword3' => [
                        'value' => $data['phone'],
                        'color' => '#173177'
                    ],
                    'keyword4' => [
                        'value' => $data['comment'],
                        'color' => '#173177'
                    ]
                ]
            ];
        }
        if ($type == 'cancel') {
            $postData = [
                'touser' => $data['weixin'],
                'template_id' => $this->template_id[$type],
                'page' => 'pages/splash/splash?order_id=' . $data['order_id'] . '&flag=' . $data['flag'],
                'form_id' => $data['form_id'],
                'data' => [
                    'keyword1' => [
                        'value' => $data['order_id'],
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' => $data['order_date'],
                        'color' => '#173177'
                    ],
                    'keyword3' => [
                        'value' => $data['status'],
                        'color' => '#173177'
                    ],
                    'keyword4' => [
                        'value' => $data['date'],
                        'color' => '#173177'
                    ],
                    'keyword5' => [
                        'value' => $data['comment'],
                        'color' => '#173177'
                    ]
                ]
            ];
        }
        if ($type == 'confirm') {
            $postData = [
                'touser' => $data['weixin'],
                'template_id' => $this->template_id[$type],
                'page' => 'pages/splash/splash?order_id=' . $data['order_id'] . '&flag=' . $data['flag'],
                'form_id' => $data['form_id'],
                'data' => [
                    'keyword1' => [
                        'value' => $data['order_id'],
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' => $data['date'],
                        'color' => '#173177'
                    ],
                    'keyword3' => [
                        'value' => $data['total_money'],
                        'color' => '#173177'
                    ],
                    'keyword4' => [
                        'value' => $data['info'],
                        'color' => '#173177'
                    ]
                ]
            ];
        }
        $result = $this->curl('post', $url, $postData);
        $result = json_decode($result, true);
        if ($result['errcode'] != 0) {
            throw new IException($result['errmsg']);
        }
        return true;
    }

    public function getWxaqrcode($shop_id)
    {
        $access_token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $access_token;
        $postData = [
            'path' => 'pages/splash/splash?shop_id=' . $shop_id,
            'width' => 300
        ];
        $result = self::curl('post', $url, $postData);
        $resultJson = json_decode($result, true);
        if ($resultJson['errcode'] != 0) {
            throw new IException($resultJson['errmsg']);
        }
        $filename = ZPHP::getRootPath() . '/webroot/weixin/weixin_' . $shop_id . '.png';
        $fp = fopen($filename, 'wb');
        fwrite($fp, $result);
        fclose($fp);
        return str_replace(ZPHP::getRootPath() . '/webroot', URL, $filename);
    }
}