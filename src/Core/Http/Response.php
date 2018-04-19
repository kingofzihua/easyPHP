<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/15
 * Time: 下午3:56
 */

namespace Core\Http;

use Conf\Event;
use Core\Http\Message\Stream;
use Core\UrlParser;
use Core\Dispatcher;
use Core\Http\Message\Status;
use Core\Http\Message\Response as HttpResponse;

/**
 * Response 类
 * Class Response
 * @package Core\Http
 */
class Response extends HttpResponse
{
    /**
     * @var int
     */
    private $isEndResponse = 0;

    /**
     * 单例模式
     * @var
     */
    protected static $instance;

    /**
     * 获取单例
     * @return Response instance
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Response();
        }
        return self::$instance;
    }

    /**
     * Response 结束
     * @return bool
     */
    public function end()
    {
        if (!$this->isEndResponse) {
            $this->isEndResponse = 1;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Response是否结束
     * @return int
     */
    public function isEndResponse()
    {
        return $this->isEndResponse;
    }

    /**
     * 写入Response
     * @param $obj
     * @return bool
     */
    public function write($obj)
    {
        /**
         * 如果已经结束的 不能再次添加了
         */
        if (!$this->isEndResponse()) {
            if (is_object($obj)) { //对象转化为字符串
                if (method_exists($obj, "__toString")) {
                    $obj = $obj->__toString();
                } else {
                    $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if (is_array($obj)) { //数组转化为字符串
                $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $this->getBody()->write($obj);

            return true;
        } else {
            trigger_error("response has end");
            return false;
        }
    }


    /**
     * Json形式返回
     * @param int $statusCode
     * @param null $result
     * @param null $msg
     * @return bool
     */
    public function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if (!$this->isEndResponse()) {
            $this->getBody()->rewind();
            $data = Array(
                "code" => $statusCode,
                "result" => $result,
                "msg" => $msg
            );
            $this->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->withStatus($statusCode);
            return true;
        } else {
            trigger_error("response has end");
            return false;
        }
    }

    /**
     * 重定向
     * @param $url
     */
    public function redirect($url)
    {
        if (!$this->isEndResponse()) {
            //仅支持header重定向  不做meta定向
            $this->withStatus(Status::CODE_MOVED_TEMPORARILY);
            $this->withHeader('Location', $url);
        } else {
            trigger_error("response has end");
        }
    }

    /**
     * 设置COOKIE
     * @param $name
     * @param null $value
     * @param null $expire
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httponly
     * @return bool
     */
    public function setCookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if (!$this->isEndResponse()) {
            //仅支持header重定向  不做meta定向
            $temp = " {$name}={$value};";
            if ($expire != null) {
                $temp .= " Expires=" . date("D, d M Y H:i:s", $expire) . ' GMT;';
                $maxAge = $expire - time();
                $temp .= " Max-Age={$maxAge};";
            }
            if ($path != null) {
                $temp .= " Path={$path};";
            }
            if ($domain != null) {
                $temp .= " Domain={$domain};";
            }
            if ($secure != null) {
                $temp .= " Secure;";
            }
            if ($httponly != null) {
                $temp .= " HttpOnly;";
            }
            $this->withAddedHeader('Set-Cookie', $temp);
            return true;
        } else {
            trigger_error("response has end");
            return false;
        }

    }

    /**
     * 程序内部跳转
     * @param $pathTo
     * @param array $attribute
     * @throws \ReflectionException
     */
    public function forward($pathTo, array $attribute = array())
    {
        if (!$this->isEndResponse()) {
            //判断跳转到地方是不是当前到路径，避免无限跳转
            if ($pathTo == UrlParser::pathInfo()) {
                trigger_error("you can not forward a request in the same path : {$pathTo}");
            } else {
                $request = Request::getInstance();
                $request->getUri()->withPath($pathTo);
                $response = Response::getInstance();
                foreach ($attribute as $key => $value) {
                    $request->withAttribute($key, $value);
                }
                Event::getInstance()->onRequest($request, $response);
                Dispatcher::getInstance()->dispatch($request, $response);
            }
        } else {
            trigger_error("response has end");
        }
    }
}