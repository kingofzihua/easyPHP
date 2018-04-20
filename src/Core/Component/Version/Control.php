<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/7/7
 * Time: 下午2:30
 */

namespace Core\Component\Version;

use Core\UrlParser;
use Core\Http\Request;
use Core\Http\Response;

/**
 * Class Control
 * @package Core\Component\Version
 */
class Control
{
    /**
     * @var array
     */
    private $map = array();

    /**
     * @var
     */
    private $defaultHandler;

    /**
     * 添加一个版本控制
     * 处理程序如果匹配版本，则必须返回布尔值true
     * @param $version
     * @param \Closure $handler
     * @return Version
     */
    function addVersion($version, \Closure $handler)
    {
        $temp = new Version();
        $this->map[$version] = array(
            "handler" => $handler,
            'version' => $temp
        );
        return $temp;
    }

    /**
     * @throws \ReflectionException
     */
    function startControl()
    {
        $request = Request::getInstance();
        $response = Response::getInstance();
        if ($response->isEndResponse()) {
            return;
        }
        if (!$request->getAttribute("version")) {
            //如果已经处于版本控制后的请求，则不再做重新匹配
            $target = null;
            foreach ($this->map as $version => $item) {
                $flag = call_user_func($item['handler'], $request, $response);
                if ($flag) {
                    $target = $item;
                    break;
                }
            }
            $pathInfo = UrlParser::pathInfo();
            if ($target) {
                $request->withAttribute("version", $version);
                $realPath = $target['version']->getPathMap($pathInfo);
                if (is_string($realPath)) {
                    $response->forward($realPath);
                } else if ($realPath instanceof \Closure) {
                    call_user_func($realPath, $request, $response);
                } else {
                    $handler = $target['version']->getDefaultHandler();
                    if (is_string($handler)) {
                        $response->forward($handler);
                    } else if ($handler instanceof \Closure) {
                        call_user_func($handler, $request, $response);
                    } else {
                        $this->defaultHandler($request, $response);
                    }
                }
                //在没有做任何响应的时候，交给defaultHandler
                if (empty($response->getStatusCode()) && $response->getBody()) {
                    $this->defaultHandler($request, $response);
                }
            }
            if ($target !== null) {
                $response->end();
            }
        }
    }

    /**
     * @param $defaultPathOrClosureHandler
     * @return $this
     */
    function setDefaultHandler($defaultPathOrClosureHandler)
    {
        $this->defaultHandler = $defaultPathOrClosureHandler;
        return $this;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws \ReflectionException
     */
    private function defaultHandler(Request $request, Response $response)
    {
        if (is_string($this->defaultHandler)) {
            $response->forward($this->defaultHandler);
        } else if ($this->defaultHandler instanceof \Closure) {
            call_user_func($this->defaultHandler, $request, $response);
        }
    }
}