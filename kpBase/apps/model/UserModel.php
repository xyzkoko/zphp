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

class UserModel
{
    public function login($params)
    {
        $staff = new medoo();
        $options = array(
            'tabName' => 'shop_staff',
            'fields' => ['shop_info.bill_zero_log', 'shop_info.shop_name', 'shop_info.shop_address(address)', 'shop_info.phone', 'shop_info.available_status',
                'shop_info.update_status', 'shop_staff.password', 'shop_staff.id', 'shop_staff.shop_id', 'shop_staff.real_name',
                'shop_staff.mob(mobile)', 'shop_staff.type', 'shop_info.print_credit_log'],
            'joins' => ['[>]shop_info' => ['shop_id' => 'id']],
            'conditions' => ['shop_staff.user_name' => $params['username']]
        );
        $result = $staff->select($options['tabName'], $options['joins'], $options['fields'], $options['conditions']);
        if (!$result) {
            throw new IException('10005');
        }
        if (count($result) == 1) {
            $result = $result['0'];
        }
        if ($result['password'] != $params['password']) {
            throw new IException('10000');
        }
        if ($result['available_status'] != 1) {
            throw new IException('10001');
        }
        if ($result['update_status'] == 0) {
            throw new IException('10002');
        }
        if ($result['update_status'] == 2) {
            throw new IException('10003');
        }
        unset($result['password']);
        unset($result['available_status']);
        unset($result['update_status']);
        return $result;
    }

}