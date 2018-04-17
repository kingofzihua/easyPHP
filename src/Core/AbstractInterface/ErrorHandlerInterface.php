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
     * @param SplError $error
     * @return mixed
     */
    public function display(SplError $error);

    /**
     * @param SplError $error
     * @return mixed
     */
    public function log(SplError $error);
}