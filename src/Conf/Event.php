<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/8
 * Time: 10:47
 */

namespace Conf;

use Core\Http\Request;
use Core\Http\Response;
use Core\Component\Spl\SplError;
use Core\AbstractInterface\AbstractEvent;

/**
 * 事件
 * Class Event
 * @package Conf
 */
class Event extends AbstractEvent
{
    /**
     * 初始化
     */
    public function frameInitialize()
    {
        // 设置默认的时区
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * 请求进来
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
        // TODO: Implement onRequest() method.
    }

    /**
     * 请求分发完毕
     * @param Request $request
     * @param Response $response
     * @param $targetControllerClass 请求的 控制器
     * @param $targetAction 请求的方法
     */
    public function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        // TODO: Implement onDispatcher() method.
    }

    /**
     * 请求响应
     * @param Request $request
     * @param Response $response
     */
    public function onResponse(Request $request, Response $response)
    {
        // TODO: Implement afterResponse() method.
    }

    /**
     * 出错了
     * @param SplError $error
     * @param $debugTrace
     */
    public function onFatalError(SplError $error, $debugTrace)
    {
        // TODO: Implement onFatalError() method.
    }
}
