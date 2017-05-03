<?php
/**
 * Created by PhpStorm.
 * Author: kx
 * Date: 2017/2/6
 * Time: 9:33
 */

namespace common;

use ZPHP\ZPHP,
    ZPHP\Core\Config;

class Redis
{
    private $redis;

    public function __construct()
    {
        $config = Config::get('cache');
        if (!empty($config['adapter'])) {
            $adapter = $config['adapter'];
        }
        $timeOut = $config['timeout'];
        $pconnect = !empty($config['pconnect']);
        $redis = new \Redis();
        if ($pconnect) {
            $redis->pconnect($config['host'], $config['port'], $timeOut);
        } else {
            $redis->connect($config['host'], $config['port'], $timeOut);
        }
        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
        if (!empty($config['auth'])) {
            $redis->auth($config['auth']);
        }
        $this->redis = $redis;
    }

    public function enable()
    {
        return true;
    }

    public function selectDb($db)
    {
        $this->redis->select($db);
    }

    public function add($key, $value, $expiration = 0)
    {
        return $this->redis->setNex($key, $expiration, $value);
    }

    public function set($key, $value, $expiration = 0)
    {
        if ($expiration) {
            return $this->redis->setex($key, $expiration, $value);
        } else {
            return $this->redis->set($key, $value);
        }
    }

    public function addToCache($key, $value, $expiration = 0)
    {
        return $this->set($key, $value, $expiration);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function getCache($key)
    {
        return $this->get($key);
    }

    public function delete($key)
    {
        return $this->redis->delete($key);
    }

    public function increment($key, $offset = 1)
    {
        return $this->redis->incrBy($key, $offset);
    }

    public function decrement($key, $offset = 1)
    {
        return $this->redis->decBy($key, $offset);
    }

    public function clear()
    {
        return $this->redis->flushDB();
    }
}