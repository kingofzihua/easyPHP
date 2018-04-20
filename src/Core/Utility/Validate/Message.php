<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/25
 * Time: 下午3:56
 */

namespace Core\Utility\Validate;


/**
 * 验证错误消息
 * Class Message
 * @package Core\Utility\Validate
 */
class Message
{
    /**
     * @var array
     */
    private $allMessages;

    /**
     * Message constructor.
     * @param array $message
     */
    function __construct(array $message)
    {
        $this->allMessages = $message;
    }

    /**
     * 是否有错误
     * @return bool
     */
    function hasError()
    {
        return !empty($this->allMessages);
    }

    /**
     * 所有的错误
     * @return array
     */
    function all()
    {
        return $this->allMessages;
    }

    /**
     * 获取指定的错误
     * @param $col
     * @return array|mixed
     */
    function get($col)
    {
        if (isset($this->allMessages[$col])) {
            return $this->allMessages[$col];
        } else {
            return array();
        }
    }
}