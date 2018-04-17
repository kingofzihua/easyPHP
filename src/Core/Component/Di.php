<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/1
 * Time: 上午12:23
 */

namespace Core\Component;

/**
 * IOC容器
 * Class Di
 * @package Core\Component
 */
class Di
{
    /**
     * 单例模式
     */
    protected static $instance;

    /**
     * 容器里存的东西
     * @var array
     */
    protected $container = array();

    /**
     * 获取单例模式
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * 添加容器
     * @param $key
     * @param $obj
     * @param array $params
     * @param bool $singleton
     * @return $this
     */
    public function set($key, $obj, array $params = array(), $singleton = true)
    {
        /**
         * 注入的时候不做任何的类型检测与转换
         * 由于编程人员为问题，该注入资源并不一定会被用到
         */
        $this->container[$key] = array(
            "obj" => $obj,
            "params" => $params,
            "singleton" => $singleton
        );

        return $this;
    }

    /**
     * 删除指定容器
     * @param $key
     */
    public function delete($key)
    {
        unset($this->container[$key]);
    }

    /**
     * 清除容器
     */
    public function clear()
    {
        $this->container = array();
    }


    /**
     * 获取容器里面的实例
     * @param $key
     * @return mixed|null|object|string
     * @throws \ReflectionException
     */
    public function get($key)
    {
        //判断当前所取的容器是否存在

        if (!isset($this->container[$key])) {
            return null;
        }

        $result = $this->container[$key];

        //判断当前容器里面的obj 是否是对象
        if (is_object($result['obj'])) {
            return $result['obj'];
        } else if (is_callable($result['obj'])) {//是闭包函数

            //执行这个方法 并把参数返回
            $ret = call_user_func_array($result['obj'], $result['params']);

            //如果singleton是 true 将返回的结果放进去
            if ($result['singleton']) {
                $this->set($key, $ret);
            }

            return $ret;
        } else if (is_string($result['obj']) && class_exists($result['obj'])) { //如果是字符串 并且是一个类的话

            //通过反射获取这个类
            $reflection = new \ReflectionClass ($result['obj']);

            //从给出的参数创建一个新的类实例。
            $ins = $reflection->newInstanceArgs($result['params']);

            //如果singleton是 true 将返回的结果放进去
            if ($result['singleton']) {
                $this->set($key, $ins);
            }

            return $ins;
        } else {
            //默认返回直接放进去的
            return $result['obj'];
        }
    }
}