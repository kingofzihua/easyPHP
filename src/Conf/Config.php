<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:48
 */

namespace Conf;

use Core\Component\Spl\SplArray;

/**
 * Class Config
 * @package Conf
 */
class Config
{
    /**
     * 单例模式
     * @var
     */
    private static $instance;

    /**
     * 配置项 数组
     * @var SplArray
     */
    protected $conf;

    /**
     * Config constructor.
     */
    private function __construct()
    {
        $conf = $this->sysConf() + $this->userConf();

        $this->conf = new SplArray($conf);
    }

    /**
     * 获取对象的实例
     * @return static
     */
    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * 获取config
     * @param $keyPath
     * @return null
     */
    function getConf($keyPath)
    {
        return $this->conf->get($keyPath);
    }

    /**
     * 设置config
     * @desc在server启动以后，无法动态的去添加，修改配置信息（进程数据独立）
     * @param $keyPath
     * @param $data
     */
    function setConf($keyPath, $data)
    {
        $this->conf->set($keyPath, $data);
    }

    /**
     * 系统默认配置
     * @return array
     */
    private function sysConf()
    {
        return array(
            "DEBUG" => array(
                "LOG" => 1,
                "DISPLAY_ERROR" => 1,
                "ENABLE" => false,
            ),
        );
    }

    /**
     * 用户配置
     * @return array
     */
    private function userConf()
    {
        return array();
    }
}