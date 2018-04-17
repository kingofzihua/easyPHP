<?php

/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/13
 * Time: 下午7:01
 */

namespace Core\Http\Message;

class Message
{
    /**
     * @var string
     */
    private $protocolVersion = '1.1'; //协议版本

    /**
     * @var array
     */
    private $headers = [];//header头信息

    /**
     * @var Stream
     */
    private $body;//内容


    /**
     * 实例化
     * Message constructor.
     * @param array|null $headers
     * @param Stream|null $body
     * @param string $protocolVersion
     */
    public function __construct(array $headers = null, Stream $body = null, $protocolVersion = '1.1')
    {
        if ($headers != null) {
            $this->headers = $headers;
        }
        if ($body != null) {
            $this->body = $body;
        }
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * 获取协议版本
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * 设置协议版本
     * @param $version
     * @return $this
     */
    public function withProtocolVersion($version)
    {
        //如果当前版本不相同 就 覆盖
        if ($this->protocolVersion !== $version) {
            $this->protocolVersion = $version;
        }

        return $this;
    }

    /**
     * 获取所有的头信息
     * @return array|null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 判断头信息是否存在
     * @param $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * 获取指定的header头
     * @param $name
     * @return array|mixed
     */
    public function getHeader($name)
    {
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : [];
    }

    /**
     * 获取HeaderLine
     * @param $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        return array_key_exists($name, $this->headers) ? implode("; ", $this->headers[$name]) : '';
    }

    /**
     * 添加或覆盖header头信息
     * @param $name
     * @param $value
     * @return $this
     */
    public function withHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        //如果不存在 或者 值不相同
        if (!isset($this->headers[$name]) || $this->headers[$name] === $value) {
            //覆盖
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * 追加header头信息
     * @param $name
     * @param $value
     * @return $this
     */
    public function withAddedHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->headers[$name] = isset($this->headers[$name]) ? array_merge($this->headers[$name], $value) : $value;

        return $this;
    }

    /**
     * 删除指定header头信息
     * @param $name
     * @return $this
     */
    public function withoutHeader($name)
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        }

        return $this;
    }

    /**
     * 获取 body
     * @return Stream|null
     */
    public function getBody()
    {
        if ($this->body == null) {
            $this->body = new Stream('');
        }

        return $this->body;
    }

    /**
     * 替换body
     * @param Stream $body
     * @return $this
     */
    public function withBody(Stream $body)
    {
        $this->body = $body;

        return $this;
    }
}