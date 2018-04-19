<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/14
 * Time: 下午12:28
 */

namespace Core\Http\Message;


/**
 * Class Response
 * @package Core\Http\Message
 */
class Response extends Message
{
    /**
     * 默认状态码
     * @var int
     */
    private $statusCode = 200;

    /**
     * @var string
     */
    private $reasonPhrase = 'OK';

    /**
     * 获取状态
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 设置状态
     * @param $code
     * @param string $reasonPhrase
     * @return $this
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        if ($code === $this->statusCode) {
            return $this;
        } else {
            $this->statusCode = $code;
            if (empty($reasonPhrase)) {
                $this->reasonPhrase = Status::getReasonPhrase($this->statusCode);
            } else {
                $this->reasonPhrase = $reasonPhrase;
            }
            return $this;
        }
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}