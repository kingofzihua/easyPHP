<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 16/9/5
 * Time: 下午8:40
 */

namespace Core\Utility;


/**
 * 随机
 * Class Random
 * @package Core\Utility
 */
class Random
{
    /**
     * 生成指定长度的随机字符串
     * @param $length
     * @return bool|string
     */
    static function randStr($length)
    {
        return substr(str_shuffle("abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789"), 0, $length);
    }

    /**
     * 生成指定长度的数字
     * @param $length
     * @return string
     */
    static function randNumStr($length)
    {
        $chars = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        );
        $password = '';
        while (strlen($password) < $length) {
            $password .= $chars[rand(0, 9)];
        }
        return $password;
    }
}