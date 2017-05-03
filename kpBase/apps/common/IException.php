<?php
/**
 * Created by PhpStorm.
 * User: kx
 * Date: 2016/10/25
 * Time: 15:01
 */
namespace common;


class IException extends \Exception
{
    public function __construct() {
        $previous = null;
        $num = func_num_args();
        $args = func_get_args();

        $message = '';
        $code = $args['0'];
        if($num == 1){
            // 自定义的代码
            $message = $this->message($args['0']);
            if(!is_numeric($code)){
                $code = -2;
            }
        }else if($num == 2){
            $message = $args['1'];
        }
        // 确保所有变量都被正确赋值
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return ['code' => $this->code, 'message' => $this->message];
    }

    public function message($code)
    {
        $messages = include 'messages.php';
        if (isset($messages[$code])) {
            return $messages[$code];
        } else {
            return $code;
        }

    }

}