<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:46
 */

namespace Core\AbstractInterface;

use Core\Component\Di;
use Core\Http\Request;
use Core\Http\Response;
use Core\Component\SysConst;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased;

/**
 * Class AbstractRouter
 * @package Core\AbstractInterface
 */
abstract class AbstractRouter
{
    /**
     * 是否缓存
     * @var bool
     */
    protected $isCache = false;

    /**
     * 缓存文件
     * @var
     */
    protected $cacheFile;

    /**
     * 路由控制器
     * @var RouteCollector
     */
    private $routeCollector;

    /**
     * AbstractRouter constructor.
     */
    public function __construct()
    {
        $this->routeCollector = new RouteCollector(new Std(), new GroupCountBased());
        $this->addRouter($this->routeCollector);
    }

    /**
     * 添加路由
     * @param RouteCollector $routeCollector
     * @return mixed
     */
    abstract function addRouter(RouteCollector $routeCollector);


    /**
     * 开启路由缓存
     * @param null $cacheFile
     * @throws \ReflectionException
     */
    public function enableCache($cacheFile = null)
    {
        $this->isCache = true;

        //设置路由缓存文件
        if ($cacheFile === null) {
            $temp = Di::getInstance()->get(SysConst::TEMP_DIRECTORY);

            $this->cacheFile = ROOT . "/{$temp}/router.cache";
        } else {
            /*
             * suggest to set a file in memory path ，such as
             * /dev/shm/ in centos 6.x~7.x
             */
            $this->cacheFile = $cacheFile;
        }
    }

    /**
     * 返回缓存文件或者是false
     * @return mixed cacheFile or boolean false
     */
    public function isCache()
    {
        if ($this->isCache) {
            return $this->cacheFile;
        } else {
            return false;
        }
    }

    /**
     * 获取路由控制器
     * @return RouteCollector
     */
    public function getRouteCollector()
    {
        return $this->routeCollector;
    }

    /**
     * 返回 request
     * @return Request
     */
    public function request()
    {
        return Request::getInstance();
    }

    /**
     * 返回 response
     * @return Response
     */
    public function response()
    {
        return Response::getInstance();
    }
}