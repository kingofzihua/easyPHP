<?php

/**
 * Created by PhpStorm.
 * User: YF
 * Date: 16/8/24
 * Time: 下午11:58
 */

namespace Core;

/**
 * 自动加载
 * Class AutoLoader
 * @package Core
 */
class AutoLoader
{
    /**
     * 单例模式
     * @var
     */
    protected static $instance;

    /**
     * 对应每个命名空间的路径前缀配置
     * @var array
     */
    protected $prefixes = array();

    /**
     * 获取单例模式
     * @return AutoLoader
     */
    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 实例化 当前类
     * AutoLoader constructor.
     */
    private function __construct()
    {
        //注册自动加载事件
        $this->register();
    }

    /**
     * 注册自动加载事件
     * @return void
     */
    protected function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * 添加命名空间
     * @param string $prefix 名称空间前缀.
     * @param string $base_dir 对应的基础路径
     * @param bool $prepend 该路径是否优先搜索
     * @return $this
     */
    public function addNamespace($prefix, $base_dir, $memorySecure = 1, $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = array();
        }
        if ($memorySecure == 1) {
            //执行清空
            $this->prefixes[$prefix] = array();
        }
        //该路径是否优先搜索 就放在前面  不然 就放在后面
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
        return $this;
    }

    /**
     * 自动加载函数
     * @param $class
     * @return bool|string
     */
    protected function loadClass($class)
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }
            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }


    /**
     * 加载映射文件
     * @param $prefix
     * @param $relative_class
     * @return bool|string
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        foreach ($this->prefixes[$prefix] as $base_dir) {
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if ($this->requireFile($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * 包含文件
     * @param $file
     * @return bool
     */
    function requireFile($file)
    {
        /**
         * 若不加ROOT，会导致在daemonize模式下类文件引入目录错误导致类无法正常加载
         * 【daemonize => 守护进程】
         */
        $file = ROOT . "/" . $file;

        if (file_exists($file)) {
            require_once "$file";
            return true;
        }

        return false;
    }
}