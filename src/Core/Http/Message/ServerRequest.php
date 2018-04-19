<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/15
 * Time: 下午1:44
 */

namespace Core\Http\Message;

/**
 * Class ServerRequest
 * @package Core\Http\Message
 */
class ServerRequest extends Request
{
    /**
     * @var array
     */
    private $attributes = [];

    /**
     * $_COOKIE
     * @var array
     */
    private $cookieParams = [];

    /**
     * $_POST
     * @var
     */
    private $parsedBody;

    /**
     * $_GET
     * @var array
     */
    private $queryParams = [];

    /**
     * $_SERVER
     * @var array
     */
    private $serverParams;

    /**
     * $_FILE
     * @var array
     */
    private $uploadedFiles = [];

    /**
     * ServerRequest constructor.
     * @param string $method
     * @param Uri|null $uri
     * @param array|null $headers
     * @param Stream|null $body
     * @param string $protocolVersion
     * @param array $serverParams
     */
    public function __construct(
        $method = 'GET', Uri $uri = null, array $headers = null,
        Stream $body = null, $protocolVersion = '1.1', $serverParams = array()
    )
    {
        $this->serverParams = $serverParams;

        parent::__construct($method, $uri, $headers, $body, $protocolVersion);
    }

    /**
     * 获取$_SERVER
     * @return array
     */
    public function getServerParams()
    {
        // TODO: Implement getServerParams() method.
        return $this->serverParams;
    }

    /**
     * 获取$_COOKIE
     * @param null $name
     * @return array|mixed|null
     */
    public function getCookieParams($name = null)
    {

        if (is_null($name)) {
            return $this->cookieParams;
        }

        return isset($this->cookieParams[$name]) ? $this->cookieParams[$name] : null;
    }

    /**
     * 设置Cookie
     * @param array $cookies
     * @return $this
     */
    public function withCookieParams(array $cookies)
    {
        $this->cookieParams = $cookies;

        return $this;
    }

    /**
     * 获取$_GET
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * 获取指定的$_GET
     * @param $name
     * @return mixed|null
     */
    public function getQueryParam($name)
    {
        $data = $this->getQueryParams();

        return isset($data[$name]) ? $data[$name] : null;
    }

    /**
     * 设置$_GET
     * @param array $query
     * @return $this
     */
    public function withQueryParams(array $query)
    {
        $this->queryParams = $query;

        return $this;
    }

    /**
     * 获取$_FILES
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * 获取指定的$_FILES
     * @param $name
     * @return mixed|null
     */
    public function getUploadedFile($name)
    {
        return isset($this->uploadedFiles[$name]) ? $this->uploadedFiles[$name] : null;
    }

    /**
     * 覆盖掉 $_FILES
     * @param array $uploadedFiles must be array of UploadFile Instance
     * @return ServerRequest
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;

        return $this;
    }

    /**
     * 获取$_POST
     * @param null $name
     * @return null
     */
    public function getParsedBody($name = null)
    {

        if (is_null($name)) {
            return $this->parsedBody;
        }

        return isset($this->parsedBody[$name]) ? $this->parsedBody[$name] : null;
    }

    /**
     * 覆盖$_POST
     * @param $data
     * @return $this
     */
    public function withParsedBody($data)
    {
        $this->parsedBody = $data;

        return $this;
    }

    /**
     * 获取所有字段
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * 获取字段值
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) === false ? $default : $this->attributes[$name];
    }

    /**
     * 添加字段值
     * @param $name
     * @param $value
     * @return $this
     */
    public function withAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * 删除字段值
     * @param $name
     * @return $this
     */
    public function withoutAttribute($name)
    {
        if (array_key_exists($name, $this->attributes) !== false) {
            unset($this->attributes[$name]);
        }

        return $this;
    }
}