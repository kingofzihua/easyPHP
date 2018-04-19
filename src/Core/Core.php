<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午5:26
 */

namespace Core;

use Conf\Event;
use Conf\Config;
use Core\Component\Di;
use Core\Http\Request;
use Core\Http\Response;
use Core\Component\SysConst;
use Core\Component\ErrorHandler;
use Core\Component\Spl\SplError;
use Core\AbstractInterface\ErrorHandlerInterface;
use Core\AbstractInterface\ExceptionHandlerInterface;

/**
 * 框架核心类
 * Class Core
 * @package Core
 */
class Core
{
    /**
     * 单例模式
     * @var
     */
    protected static $instance;

    /**
     * 获取 单例模式
     * @param callable $preHandler callable before frameWork initialize
     * @return Core instance
     */
    public static function getInstance(callable $preHandler = null)
    {
        //如果已经被实例化了 不在进行实例化
        if (!isset(self::$instance)) {
            self::$instance = new static($preHandler);
        }

        return self::$instance;
    }

    /**
     * 初始化的时候 接受一个匿名函数作为参数
     * @param callable $preHandler callable before frameWork initialize
     */
    private function __construct(callable $preHandler = null)
    {
        if (is_callable($preHandler)) {
            call_user_func($preHandler);
        }
    }


    /**
     * 运行
     */
    public function run()
    {
        //获取Request单例 和 Response单例
        $request = Request::getInstance();
        $response = Response::getInstance();

        //onRequest事件
        Event::getInstance()->onRequest($request, $response);

        //调度
        Dispatcher::getInstance()->dispatch($request, $response);

        $status = $response->getStatusCode();//获取返回的状态码
        $reason = $response->getReasonPhrase();//获取
        //状态码有固定格式。
        header('HTTP/1.1 ' . $status . ' ' . $reason);
        // 确保FastCGI模式下正常
        header('Status:' . $status . ' ' . $reason);
        $headers = $response->getHeaders();
        foreach ($headers as $header => $val) {
            foreach ($val as $sub) {
                header($header . ':' . $sub);
            }
        }
        echo $response->getBody()->__toString(); //response转化为字符串
        $response->getBody()->close();//response关闭
        Event::getInstance()->onResponse($request, $response);//触发Response响应事件
    }


    /**
     * 框架初始化
     * @return $this
     * @throws \ReflectionException
     */
    public function frameWorkInitialize()
    {
        //定义框架跟路径ROOT目录
        $this->defineSysConst();

        //注册自动加载
        $this->registerAutoLoader();

        //设置程序默认的目录
        $this->setDefaultAppDirectory();

        //初始化事件
        Event::getInstance()->frameInitialize();

        //初始化系统所需要的目录
        $this->sysDirectoryInit();

        //注册错误处理函数
        $this->registerErrorHandler();

        //注册异常处理
        $this->registerExceptionHandler();

        //返回当前对象
        return $this;
    }

    /**
     * 注册PHP文件自动加载
     * @return void
     */
    private function registerAutoLoader()
    {
        //包含 文件自动加载类
        require_once __DIR__ . "/AutoLoader.php";

        //获取自动加载实例
        $loader = AutoLoader::getInstance();

        //添加系统核心目录
        $loader->addNamespace("Core", "Core");

        //添加conf目录
        $loader->addNamespace("Conf", "Conf");

        //添加系统依赖组件
        $loader->addNamespace("FastRoute", "Core/Vendor/FastRoute");//路由
        $loader->addNamespace("SuperClosure", "Core/Vendor/SuperClosure"); //闭包函数 ？干嘛用的？
        $loader->addNamespace("PhpParser", "Core/Vendor/PhpParser"); //解析器
    }

    /**
     * 定义框架跟路径ROOT目录
     */
    private function defineSysConst()
    {
        define("ROOT", realpath(__DIR__ . '/../'));
    }

    /**
     * 初始化系统所需要的目录
     * @return void
     */
    private function sysDirectoryInit()
    {
        //获取临时目录路径
        $tempDir = Di::getInstance()->get(SysConst::TEMP_DIRECTORY);

        //如果没有 默认为 Temp
        if (empty($tempDir)) {
            $tempDir = ROOT . "/Temp";
            Di::getInstance()->set(SysConst::TEMP_DIRECTORY, $tempDir);
        }

        //判断该路径是否存在
        if (!is_dir($tempDir)) {
            //没有就创建目录
            if (!mkdir($tempDir, 0755, true)) {
                die("create Temp Directory:{$tempDir} fail");
            }
        }

        //创建默认日志目录
        $logDir = ROOT . "/Log";
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0755, true)) {
                die("create log Directory:{$logDir} fail");
            }
        }
    }

    /**
     * 注册错误处理函数
     * @return void
     */
    private function registerErrorHandler()
    {
        //获取配置DEBUG
        $conf = Config::getInstance()->getConf("DEBUG");

        //判断是否开启调试模式？
        if ($conf['ENABLE'] == true) {
            //注册错误处理类
            set_error_handler(function ($errorCode, $description, $file = null, $line = null, $context = null) use ($conf) {

                //实例化一个标准错误类
                $error = new SplError();
                $error->setErrorCode($errorCode);
                $error->setDescription($description);
                $error->setFile($file);
                $error->setLine($line);
                $error->setContext($context);

                $errorHandler = Di::getInstance()->get(SysConst::DI_ERROR_HANDLER);

                //如果对象属于该类或该类是此对象的父类则返回
                if (!is_a($errorHandler, ErrorHandlerInterface::class)) {

                    //实例化一个错误处理类
                    $errorHandler = new ErrorHandler();
                }

                //把错误 注入到 错误处理类
                $errorHandler->handler($error);

                //如果 错误需要显示
                if ($conf['DISPLAY_ERROR'] == true) {
                    //输出错误
                    $errorHandler->display($error);
                }

                //如果错误需要记录
                if ($conf['LOG'] == true) {
                    //记录错误
                    $errorHandler->log($error);
                }
            });
        }
    }

    /**
     * 注册异常处理函数
     * @throws \ReflectionException
     */
    private function registerExceptionHandler()
    {
        //获取异常处理函数
        $handler = Di::getInstance()->get(SysConst::DI_EXCEPTION_HANDLER);

        //如果被重写掉 则用户定义的异常处理
        if ($handler instanceof ExceptionHandlerInterface) {
            set_exception_handler(function (\Exception $exception) use ($handler) {
                $handler->handler($exception);
            });
        }
    }

    /**
     * 设置默认的程序目录
     */
    private function setDefaultAppDirectory()
    {
        //获取定义的程序目录
        $dir = Di::getInstance()->get(SysConst::APPLICATION_DIR);

        //没有的话就是APP
        if (empty($dir)) {
            $dir = "App";
            Di::getInstance()->set(SysConst::APPLICATION_DIR, $dir);
        }

        $prefix = $dir;

        //添加命名空间
        AutoLoader::getInstance()->addNamespace($prefix, $dir);
    }
}