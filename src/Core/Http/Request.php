<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/15
 * Time: 下午3:55
 */

namespace Core\Http;

use Core\UrlParser;
use Core\Http\Message\Uri;
use Core\Http\Message\Stream;
use Core\Http\Message\UploadFile;
use Core\Utility\Validate\Validate;
use Core\Http\Message\ServerRequest;

/**
 * Request对象
 * Class Request
 * @package Core\Http
 */
class Request extends ServerRequest
{
    /**
     * 单例模式
     * @var
     */
    protected static $instance;

    /**
     * 获取Request对象的单例
     * @return Request instance
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Request();
        }

        return self::$instance;
    }

    /**
     * 实例化Request对象
     * @return void
     */
    public function __construct()
    {
        //初始化headers
        $this->initHeaders();

        //初始化uri
        $uri = $this->initUri();

        //获取请求类型 GET还是 POST
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

        //获取协议版本
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';

        //获取请求的内容
        $body = new Stream(fopen("php://input", "r+"));

        //获取文件
        $files = $this->initFiles();

        parent::__construct($method, $uri, null, $body, $protocol, $_SERVER);

        $this->withCookieParams($_COOKIE)->withQueryParams($_GET)->withParsedBody($_POST)->withUploadedFiles($files);
    }

    public function getRequestParam($keyOrKeys = null, $default = null)
    {
        if ($keyOrKeys !== null) {
            if (is_string($keyOrKeys)) {
                $ret = $this->getParsedBody($keyOrKeys);
                if ($ret === null) {
                    $ret = $this->getQueryParam($keyOrKeys);
                    if ($ret === null) {
                        if ($default !== null) {
                            $ret = $default;
                        }
                    }
                }
                return $ret;
            } else if (is_array($keyOrKeys)) {
                if (!is_array($default)) {
                    $default = array();
                }
                $data = $this->getRequestParam();
                $keysNull = array_fill_keys(array_values($keyOrKeys), null);
                if ($keysNull === null) {
                    $keysNull = [];
                }
                $all = array_merge($keysNull, $default, $data);
                $all = array_intersect_key($all, $keysNull);
                return $all;
            } else {
                return null;
            }
        } else {
            return array_merge($this->getParsedBody(), $this->getQueryParams());
        }
    }

    public function requestParamsValidate(Validate $validate)
    {
        return $validate->validate($this->getRequestParam());
    }

    /**
     * 初始化URI
     * @return Uri
     */
    private function initUri()
    {
        $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '')
            . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $uri = new Uri($url);
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $uri->withUserInfo($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        }
        return $uri;
    }

    /**
     * 初始化Files
     * @return array
     */
    private function initFiles()
    {
        $normalized = array();
        foreach ($_FILES as $key => $value) {
            $normalized[$key] = new UploadFile(
                $value['tmp_name'],
                (int)$value['size'],
                (int)$value['error'],
                $value['name'],
                $value['type']
            );
        }
        return $normalized;
    }

    /**
     * 初始化Headers
     * @return void
     */
    private function initHeaders()
    {
        // 获取全部 HTTP 请求头信息
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        foreach ($headers as $header => $val) {
            //添加到headers去
            $this->withAddedHeader($header, $val);
        }
    }
}