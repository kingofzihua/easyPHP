<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/8
 * Time: 下午8:06
 */

namespace Core\Utility\Validate;

use Core\Component\Spl\SplBean;

/**
 * Class ColumnBean
 * @package Core\Utility\Validate
 */
class ColumnBean extends SplBean
{
    /**
     * @var
     */
    protected $errorMsg;
    /**
     * @var array
     */
    protected $ruleMap = array();

    /**
     * 错误消息
     * @param $msg
     * @return $this
     */
    function withErrorMsg($msg)
    {
        $this->errorMsg = $msg;

        return $this;
    }

    /**
     * 添加规则
     * @param $rule
     * @param array $args
     * @param null $errorMsg
     * @return $this
     */
    function addRule($rule, array $args = array(), $errorMsg = null)
    {
        $this->ruleMap[$rule] = array(
            "args" => $args,
            "msg" => $errorMsg,
        );
        return $this;
    }

    /**
     * 初始化
     * @return mixed|void
     */
    protected function initialize()
    {
    }
}