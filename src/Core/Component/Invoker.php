<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/5/9
 * Time: 上午11:20
 */

namespace Core\Component;


/**
 * Class Invoker
 * @package Core\Component
 */
class Invoker
{

    /**
     * 当使用这个函数的时候 要使用try catch
     * @param callable $callable
     * @param array $arguments
     * @param int $timeout
     * @return mixed
     * @throws \Exception
     */
    static function exec(callable $callable, array $arguments = array(), $timeout = 1)
    {
        if (phpversion() >= '7.1') {
            // support from php  7.1
            pcntl_async_signals(true);
        }
        //注册信号量
        pcntl_signal(
            SIGALRM,
            function () use ($timeout) {
                throw new \Exception("Execution aborted after {$timeout} seconds");
            },
            true
        );
        pcntl_alarm($timeout);
        try {
            $result = call_user_func_array($callable, $arguments);
        } catch (\Exception $t) {
            pcntl_alarm(0);
            throw $t;
        }
        pcntl_alarm(0);
        return $result;
    }
}