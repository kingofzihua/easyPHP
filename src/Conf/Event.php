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

    public function onRequest(Request $request, Response $response)
    {
        // TODO: Implement onRequest() method.
    }

    public function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        // TODO: Implement onDispatcher() method.
    }

    public function onResponse(Request $request, Response $response)
    {
        // TODO: Implement afterResponse() method.
    }

    public function onFatalError(SplError $error, $debugTrace)
    {
        // TODO: Implement onFatalError() method.
    }
}
