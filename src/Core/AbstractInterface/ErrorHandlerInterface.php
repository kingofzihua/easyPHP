<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:14
 */

namespace Core\AbstractInterface;


use Core\Component\Spl\SplError;

/**
 * 错误处理接口
 * Interface ErrorHandlerInterface
 * @package Core\AbstractInterface
 */
interface ErrorHandlerInterface
{
    /**
     * @param SplError $error
     * @return mixed
     */
    public function handler(SplError $error);

    /**
     * 错误输出
     * @param SplError $error
     * @return mixed
     */
    public function display(SplError $error);

    /**
     * 错误记录到日志
     * @param SplError $error
     * @return mixed
     */
    public function log(SplError $error);
}