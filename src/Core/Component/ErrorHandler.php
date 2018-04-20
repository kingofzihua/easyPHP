<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/19
 * Time: 上午9:51
 */

namespace Core\Component;

use Core\Http\Request;
use Core\Http\Response;
use Core\Component\Spl\SplError;
use Core\AbstractInterface\ErrorHandlerInterface;

/**
 * 错误处理
 * Class ErrorHandler
 * @package Core\Component
 */
class ErrorHandler implements ErrorHandlerInterface
{

    /**
     * @param SplError $error
     * @return mixed|void
     */
    public function handler(SplError $error)
    {
        // TODO: Implement handler() method.
    }

    /**
     * @param SplError $error
     * @return mixed|void
     */
    public function display(SplError $error)
    {
        //判断 是不是浏览器过来的请求啊， 不是单元测试 并且不是CLI模式的时候 是输出到页面的
        if (Request::getInstance()) {
            //写入 Response
            Response::getInstance()->write($error->__toString());
        } else {
            //控制台输出
            Logger::getInstance()->console($error, 0);
        }
    }

    /**
     * 日志
     * @param SplError $error
     * @return mixed|void
     */
    public function log(SplError $error)
    {
        // TODO: Implement log() method.
        Logger::getInstance()->log($error);
    }
}