<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 14:06
 */
class MY_Service
{
    public function __construct()
    {
        log_message('debug', "Service Class Initialized");

    }

    function __get($key)
    {
        $CI = & get_instance();
        return $CI->$key;
    }
}