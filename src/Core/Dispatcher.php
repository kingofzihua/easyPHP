<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:45
 */

namespace Core;

use Conf\Event;
use Core\Component\Di;
use Core\Http\Request;
use Core\Http\Response;
use Core\Component\SysConst;
use Core\Http\Message\Status;
use Core\Component\SuperClosure;
use Core\Component\RouteCollector;
use FastRoute\Dispatcher\GroupCountBased;
use Core\AbstractInterface\AbstractRouter;
use Core\AbstractInterface\AbstractController;

/**
 * Class Dispatcher
 * @package Core
 */
class Dispatcher
{
    /**
     * 单例模式
     * @var
     */
    protected static $instance;

    /**
     * 程序根目录
     * @var mixed|null|object|string
     */
    private $appDirectory;


    /**
     * @return static
     * @throws \ReflectionException
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Dispatcher constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
        //定义程序根目录
        $this->appDirectory = Di::getInstance()->get(SysConst::APPLICATION_DIR);
    }

    /**
     * 调度
     * @param Request $request
     * @param Response $response
     * @throws \ReflectionException
     */
    public function dispatch(Request $request, Response $response)
    {
        //如果 请求结束了就不在进行调度了
        if ($response->isEndResponse()) {
            return;
        }

        //获取URL链接信息 /kingofhua/php
        $pathInfo = UrlParser::pathInfo();

        $routeInfo = $this->fastRouter($pathInfo, $request->getMethod()); //false

        if ($routeInfo !== false) {
            switch ($routeInfo[0]) {
                case \FastRoute\Dispatcher::NOT_FOUND:
                    // ... 404 NdoDispatcherot Found 方法不在
                    break;
                case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    //方法 不允许访问
                    $response->withStatus(Status::CODE_METHOD_NOT_ALLOWED);
                    break;
                case \FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    if (is_callable($handler)) {
                        call_user_func_array($handler, $vars);
                    }
                    break;
            }
        }

        //判断如果请求结束了
        if ($response->isEndResponse()) {
            return;
        }

        //去除为fastRouter预留的左边斜杠 kingofhua/php
        $pathInfo = ltrim($pathInfo, "/");
        $list = explode("/", $pathInfo);// kingofhua php
        $controllerNameSpacePrefix = "{$this->appDirectory}\\Controller";
        $actionName = null; //获取到的操作名
        $finalClass = null; //获取找到的类名

        //获取 控制器最大深度
        $controlMaxDepth = Di::getInstance()->get(SysConst::CONTROLLER_MAX_DEPTH);

        if (intval($controlMaxDepth) <= 0) { //不正常的数据 默认3
            $controlMaxDepth = 3;
        }

        //最大深度
        $maxDepth = count($list) < $controlMaxDepth ? count($list) : $controlMaxDepth;

        $isIndexController = 0;

        while ($maxDepth > 0) {//2 1
            $className = '';
            for ($i = 0; $i < $maxDepth; $i++) {// $i [0 1 2] [0 1]
                $className = $className . "\\" . ucfirst($list[$i]);//为一级控制器Index服务
            }

            // App\Controller\Kingofhua\Php  App\Controller\Kingofhua
            if (class_exists($controllerNameSpacePrefix . $className)) {
                //尝试获取该class后的actionName
                $actionName = isset($list[$i]) ? $list[$i] : ''; //php
                $finalClass = $controllerNameSpacePrefix . $className;
                break;
            } else {
                //尝试搜搜index控制器
                $temp = $className . "\\Index";
                // App\Controller\Kingofhua\Php\Index 这里应该是默认跟路径的时候用的，毕竟没有人会省略一个index类名
                if (class_exists($controllerNameSpacePrefix . $temp)) {
                    $finalClass = $controllerNameSpacePrefix . $temp;
                    //尝试获取该class后的actionName
                    $actionName = isset($list[$i]) ? $list[$i] : null;
                    break;
                }
            }
            $maxDepth--;
        }

        //若无法匹配完整控制器   搜搜Index控制器是否存在
        if (empty($finalClass)) {
            $finalClass = $controllerNameSpacePrefix . "\\Index";
            $isIndexController = 1; //是默认的index
        }

        //判断这个类是否存在
        if (class_exists($finalClass)) {
            if ($isIndexController) {
                $actionName = isset($list[0]) ? $list[0] : '';
            }
            $actionName = $actionName ? $actionName : "index"; //默认是index方法

            $controller = new $finalClass;

            //判断当前类 是否是继承自 控制器
            if ($controller instanceof AbstractController) {

                Event::getInstance()->onDispatcher($request, $response, $finalClass, $actionName);

                //预防在进控制器之前已经被拦截处理
                if (!Response::getInstance()->isEndResponse()) {
                    //请求方法
                    $controller->__call($actionName, null);
                }

            } else {
                //  如果类不是控制器 404
                Response::getInstance()->withStatus(Status::CODE_NOT_FOUND);
                trigger_error("controller {$finalClass} is not a instance of AbstractController");
            }
        } else {
            //  如果类没有找到 404
            Response::getInstance()->withStatus(Status::CODE_NOT_FOUND);
            trigger_error("default controller Index not implement");
        }
    }

    /**
     * 快速路由？
     * @param $pathInfo
     * @param $requestMethod
     * @return array|bool
     * @throws \ReflectionException
     */
    public function fastRouter($pathInfo, $requestMethod)
    {
        try {
            /**
             * if exit Router class in App directory
             */
            $ref = new \ReflectionClass("{$this->appDirectory}\\Router");
            $router = $ref->newInstance();

            //判断有没有重写 AbstractRouter ？

            if ($router instanceof AbstractRouter) {

                //是否缓存路由
                $is = $router->isCache();
                if ($is) {
                    // ROOT . /temp/router.cache.app ？
                    $is = $is . ".{$this->appDirectory}";

                    //判断该文件是否存在
                    if (file_exists($is)) {
                        $dispatcherData = file_get_contents($is);
                        //unserialize() 对单一的已序列化的变量进行操作，将其转换回 PHP 的值。
                        $dispatcherData = unserialize($dispatcherData);
                    } else {
                        //文件存在
                        $dispatcherData = $router->getData();

                        //获取所有的路由数据 数组形式
                        $cache = $dispatcherData;
                        /*
                         * to support closure 支持关闭
                         *
                         * array_walk_recursive 对数组中的每个成员递归地应用用户函数
                         */
                        array_walk_recursive($cache, function (&$item, $key) { //数组键 数组值
                            if ($item instanceof \Closure) {
                                $item = new SuperClosure($item);
                            }
                        });

                        //缓存

                        file_put_contents($is, serialize($cache));
                    }

                    $fastRouterDispatcher = new GroupCountBased($dispatcherData);

                    return $fastRouterDispatcher->dispatch($requestMethod, $pathInfo);
                } else {
                    $fastRouterDispatcher = new GroupCountBased($router->getData());
                    return $fastRouterDispatcher->dispatch($requestMethod, $pathInfo);
                }
            }
        } catch (\Exception $e) {

        }
        return false;
    }
}