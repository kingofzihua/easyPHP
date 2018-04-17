<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/8
 * Time: 10:41
 */

namespace Core\AbstractInterface;

use Core\Http\Request;
use Core\Http\Response;
use Core\Component\Spl\SplError;

/**
 * 事件抽象类
 * Class AbstractEvent
 * @package Core\AbstractInterface
 */
abstract class AbstractEvent
{
    protected static $instance;

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    abstract public function frameInitialize();

    abstract public function onRequest(Request $request, Response $response);

    abstract public function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction);

    abstract public function onResponse(Request $request, Response $response);

    abstract public function onFatalError(SplError $error, $debugTrace);
}