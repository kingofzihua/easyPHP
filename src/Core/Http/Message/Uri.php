<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/14
 * Time: 下午12:32
 */

namespace Core\Http\Message;

/**
 * URI 处理
 * @url https://kingofzihua:********@gitee.com/kingofhua/php.git?name=kingofzihua&age=12#KING
 * Class Uri
 * @package Core\Http\Message
 */
class Uri
{
    /**
     * gitee.com
     * @var string
     */
    private $host;

    /**
     * kingofzihua:********
     * @var string
     */
    private $userInfo;

    /**
     * 端口 80
     * @var int
     */
    private $port = 80;

    /**
     * /kingofhua/php.git
     * @var string
     */
    private $path;

    /**
     * name=kingofzihua&age=12
     * @var string
     */
    private $query;

    /**
     * KING
     * @var string
     */
    private $fragment;

    /**
     * https
     * @var string
     */
    private $scheme;

    /**
     * Uri constructor.
     * @param string $url
     */
    public function __construct($url = '')
    {
        if ($url !== '') {
            $parts = parse_url($url);
            $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
            $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
            $this->host = isset($parts['host']) ? $parts['host'] : '';
            $this->port = isset($parts['port']) ? $parts['port'] : 80;
            $this->path = isset($parts['path']) ? $parts['path'] : '';
            $this->query = isset($parts['query']) ? $parts['query'] : '';
            $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
        }
    }

    /**
     * 获取 Schem
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * 获取 登陆用户
     * @demo kingofzihua:********@gitee.com:80
     * @return string
     */
    public function getAuthority()
    {
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    /**
     * 获取userInfo
     * @return string
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * 获取 host
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * 获取fragment
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param $scheme
     * @return $this
     */
    public function withScheme($scheme)
    {
        if ($this->scheme === $scheme) {
            return $this;
        }
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @param $user
     * @param null $password
     * @return $this
     */
    public function withUserInfo($user, $password = null)
    {
        $info = $password != '' ? $user . ':' . $password : $user;

        if ($this->userInfo !== $info) {
            $this->userInfo = $info;
        }

        return $this;
    }

    /**
     * @param $host
     * @return $this
     */
    public function withHost($host)
    {
        $host = strtolower($host);

        if ($this->host !== $host) {
            $this->host = $host;
        }

        return $this;
    }

    /**
     * @param $port
     * @return $this
     */
    public function withPort($port)
    {
        if ($this->port === $port) {
            return $this;
        }
        $this->port = $port;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function withPath($path)
    {
        if ($this->path === $path) {
            return $this;
        }
        $this->path = $path;
        return $this;
    }

    /**
     * @param $query
     * @return $this
     */
    public function withQuery($query)
    {
        if ($this->query === $query) {
            return $this;
        }
        $this->query = $query;
        return $this;
    }

    /**
     * @param $fragment
     * @return $this
     */
    public function withFragment($fragment)
    {
        if ($this->fragment !== $fragment) {
            $this->fragment = $fragment;
        }

        return $this;
    }

    /**
     * 转化为对象形式
     * @return string
     */
    public function __toString()
    {
        $uri = '';
        // weak type checks to also accept null until we can add scalar type hints
        if ($this->scheme != '') {
            $uri .= $this->scheme . ':';
        }
        if ($this->getAuthority() != '' || $this->scheme === 'file') {
            $uri .= '//' . $this->getAuthority();
        }
        $uri .= $this->path;
        if ($this->query != '') {
            $uri .= '?' . $this->query;
        }
        if ($this->fragment != '') {
            $uri .= '#' . $this->fragment;
        }
        return $uri;
    }
}