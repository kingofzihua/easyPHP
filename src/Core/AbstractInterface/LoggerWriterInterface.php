<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午5:40
 */

namespace Core\AbstractInterface;


/**
 * 日志接口
 * Interface LoggerWriterInterface
 * @package Core\AbstractInterface
 */
interface LoggerWriterInterface
{
    /**
     * @param $obj
     * @param $logCategory
     * @param $timeStamp
     * @return mixed
     */
    static function writeLog($obj, $logCategory, $timeStamp);
}