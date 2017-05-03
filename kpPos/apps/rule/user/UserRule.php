<?php
namespace rule\user;

use common\Rule;

class UserRule extends Rule
{
    public function loginRule($data)
    {
        $rule = array(
            'required' => array(
                'platform',
                'username',
                'password'
            )
        );
        $this->validate($rule,$data);
    }

    public function attributeLabels()
    {
        return [
            'platform' => '平台编号',
            'username' => '登陆用户名',
            'password' => '密码'
        ];
    }

}

