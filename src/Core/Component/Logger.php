<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/1
 * Time: 上午1:32
 */

namespace Core\Component;

use Core\AbstractInterface\LoggerWriterInterface;

/**
 * 日志
 * Class Logger
 * @package Core\Component
 */
class Logger
{
    /**
     * 单例模式
     * @var
     */
    private static $instance;

    /**
     * 日志的类型
     * @var string
     */
    private static $logCategory = 'default';

    /**
     * 获取单例模式
     * @param string $logCategory
     * @return static
     */
    public static function getInstance($logCategory = 'default')
    {
        self::$logCategory = $logCategory;

        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }


    /**
     * 记录日志
     * @param $obj
     * @throws \ReflectionException
     */
    public function log($obj)
    {
        //获取我定义的 日志类
        $loggerWriter = Di::getInstance()->get(SysConst::DI_LOGGER_WRITER);

        //判断是否走自定义的 日志写入类
        if ($loggerWriter instanceof LoggerWriterInterface) {
            $loggerWriter::writeLog($obj, self::$logCategory, time());
        } else {
            //转化为字符串
            $obj = $this->objectToString($obj);

            /*
             * default method to save log
             */

            //拼接显示形式
            $str = "time : " . date("y-m-d H:i:s") . " message: " . $obj . "\n";
            $filePrefix = self::$logCategory . "_" . date('ym');
            $filePath = ROOT . "/Log/{$filePrefix}_log.txt";
            //写入文件
            file_put_contents($filePath, $str, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * 控制台输出
     * @param $obj
     * @param int $saveLog
     * @throws \ReflectionException
     */
    public function console($obj, $saveLog = 1)
    {
        $obj = $this->objectToString($obj);
        echo $obj . "\n";
        if ($saveLog) {
            $this->log($obj);
        }
    }

    /**
     * 对象转化为字符串
     * @param $obj
     * @return mixed|string
     */
    private function objectToString($obj)
    {
        if (is_object($obj)) {
            if (method_exists($obj, "__toString")) {
                $obj = $obj->__toString();
            } else if (method_exists($obj, 'jsonSerialize')) {
                $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $obj = var_export($obj, true);
            }
        } else if (is_array($obj)) {
            $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return $obj;
    }
}