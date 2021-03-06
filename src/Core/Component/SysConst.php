<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/3
 * Time: 下午7:38
 */

namespace Core\Component;


/**
 * 系统预定义常量
 * Class SysConst
 * @package Core\Component
 */
class SysConst
{
    /*
     * DI开头为依赖注入键值名称
     */
    const DI_ERROR_HANDLER = 'DI_ERROR_HANDLER';//错误处理类
    const DI_LOGGER_WRITER = 'DI_LOGGER_WRITER'; //日志
    const DI_SESSION_HANDLER = 'DI_SESSION_HANDLER';
    const CONTROLLER_MAX_DEPTH = 'CONTROLLER_MAX_DEPTH'; //控制器最大深度
    const APPLICATION_DIR = 'APPLICATION_DIR';//定义应用目录（以便支持例如多域名部署需求）
    const SHARE_MEMORY_FILE = 'SHARE_MEMORY_FILE';
    const TEMP_DIRECTORY = 'TEMP_DIRECTORY'; //缓存的路径
    const VERSION_CONTROL = 'VERSION_CONTROL';
    const DI_EXCEPTION_HANDLER = 'DI_EXCEPTION_HANDLER'; //异常处理类
}