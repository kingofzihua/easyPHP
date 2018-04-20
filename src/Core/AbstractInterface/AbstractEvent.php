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
    /**
     * @var
     */
    protected static $instance;

    /**
     * @return static
     */
    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 初始化事件
     * @return mixed
     */
    abstract public function frameInitialize();

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    abstract public function onRequest(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     * @param $targetControllerClass
     * @param $targetAction
     * @return mixed
     */
    abstract public function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction);

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    abstract public function onResponse(Request $request, Response $response);

    /**
     * @param SplError $error
     * @param $debugTrace
     * @return mixed
     */
    abstract public function onFatalError(SplError $error, $debugTrace);
}