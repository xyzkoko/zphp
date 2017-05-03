<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/10/24
 * Time: 18:39
 */
namespace common;


abstract class Rule
{
    /**|rules 规则：(可自定义)
     * //        array(
     * //            'required' => array(
     * //                'id',
     * //                'name'
     * //            ),
     * //            'string' => array(
     * //                'name'
     * //            )
     * //        );
     * */
    //abstract public function rules();

    abstract public function attributeLabels();

    public function validate($rules,$data)
    {
        foreach ($rules as $key => $val) {
            $this->$key($val, $data);
        }
    }

    public function getLabels($key)
    {
        $labels = $this->attributeLabels();
        if (isset($labels[$key])) {
            return $labels[$key];
        } else {
            return $key;
        }
    }

    public function required($rule, $data)
    {
        foreach ($rule as $val) {
            if (!isset($data[$val]) || $data[$val] == '') {
                throw new IException($this->getLabels($val) . '不能为空');
            }
        }
        return true;
    }

    public function string($rule, $data)
    {
        foreach ($rule as $val) {
            if (isset($data[$val]) && !is_string($data[$val])) {
                throw new IException($this->getLabels($val) . '必须为string类型');
            }
        }
        return true;
    }

    public function phone($rule, $data)
    {
        $matchRule = '/^[1][358][0-9]{9}$/';
        foreach ($rule as $val) {
            if (isset($data[$val]) && !preg_match($matchRule, $data[$val])) {
                throw new IException($this->getLabels($val) . '不符合手机号格式');
            }
        }
    }

    public function number($rule, $data)
    {
        foreach ($rule as $val) {
            if (isset($data[$val]) && !is_numeric($data[$val])) {
                throw new IException($this->getLabels($val) . '必须为数字');
            }
        }
    }

    public function money($rule, $data)
    {
        foreach ($rule as $val) {
            if (isset($data[$val]) && $data[$val] > 99999999) {
                throw new IException($this->getLabels($val) . '超过最大金额数');
            }
            if (isset($data[$val]) && $data[$val] < 0) {
                throw new IException($this->getLabels($val) . '不能小于0');
            }
        }
    }
    public function reserve($rule, $data)
    {
        foreach ($rule as $val) {
            if (isset($data[$val]) && $data[$val] > 99999999999) {
                throw new IException($this->getLabels($val) . '超过最大库存数');
            }
        }
    }
}