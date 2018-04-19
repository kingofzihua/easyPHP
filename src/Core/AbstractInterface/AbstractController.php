<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:42
 */

namespace Core\AbstractInterface;

use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Message\Status;

/**
 * 控制器抽象类
 * Class AbstractController
 * @package Core\AbstractInterface
 */
abstract class AbstractController
{
    /**
     * 当前请求的方法
     * @var null
     */
    protected $actionName = null;
    /**
     * @var null
     */
    protected $callArgs = null;

    /**
     * 获取当前请求的 方法
     * @param null $actionName
     * @return null
     */
    public function actionName($actionName = null)
    {
        if ($actionName === null) {
            return $this->actionName;
        } else {
            $this->actionName = $actionName;
        }
    }

    /**
     * @return mixed
     */
    abstract function index();

    /**
     * @param $actionName
     * @return mixed
     */
    abstract function onRequest($actionName);

    /**
     * @param null $actionName
     * @param null $arguments
     * @return mixed
     */
    abstract function actionNotFound($actionName = null, $arguments = null);

    /**
     * @return mixed
     */
    abstract function afterAction();

    /**
     * @return Request
     */
    public function request()
    {
        return Request::getInstance();
    }

    /**
     * @return Response
     */
    public function response()
    {
        return Response::getInstance();
    }

    /**
     * @param $actionName
     * @param $arguments
     */
    public function __call($actionName, $arguments)
    {
        /**
         * 防止恶意调用
         * actionName、onRequest、actionNotFound、afterAction、request
         * response、__call
         */
        if (in_array($actionName, array(
            'actionName', 'onRequest', 'actionNotFound', 'afterAction', 'request', 'response', '__call'
        ))) {
            //500
            $this->response()->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
            return;
        }

        //执行onRequest事件
        $this->actionName($actionName);

        $this->onRequest($actionName); //执行前置中间函数

        //判断是否被拦截
        if (!$this->response()->isEndResponse()) {
            //判断当前的方法是否被定义
            if (method_exists($this, $actionName)) {
                $realName = $this->actionName();
                $this->$realName();
            } else {
                //没有被定义的时候 返回一个默认的
                $this->actionNotFound($actionName, $arguments);
            }
        }
        //执行完毕后 执行后置中间函数
        $this->afterAction();
    }
}