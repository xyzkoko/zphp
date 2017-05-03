<?php

    return array(
        'pdo' => array(
            'database_type' => 'mysql',
            'database_name' => 'wholesale_saas',
            'server' => '192.168.10.192',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',

            // 可选参数
            'port' => 3306,

            // 可选，定义表的前缀
//            'prefix' => 'PREFIX_',

            // 连接参数扩展, 更多参考 http://www.php.net/manual/en/pdo.setattribute.php
//            'option' => [
//                PDO::ATTR_CASE => PDO::CASE_NATURAL
//            ]

        ),
    );
