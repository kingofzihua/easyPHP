<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/15
 * Time: 下午3:56
 */

namespace Core\Http;

use Conf\Event;
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

    public function end()
    {
        if (!$this->isEndResponse) {
            $this->isEndResponse = 1;
            return true;
        } else {
            return false;
        }
    }

    public function isEndResponse()
    {
        return $this->isEndResponse;
    }

    public function write($obj)
    {
        if (!$this->isEndResponse()) {
            if (is_object($obj)) {
                if (method_exists($obj, "__toString")) {
                    $obj = $obj->__toString();
                } else {
                    $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if (is_array($obj)) {
                $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $this->getBody()->write($obj);
            return true;
        } else {
            trigger_error("response has end");
            return false;
        }
    }

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

    public function forward($pathTo, array $attribute = array())
    {
        if (!$this->isEndResponse()) {
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