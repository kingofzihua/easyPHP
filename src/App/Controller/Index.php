<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/20
 * Time: 下午12:15
 */

namespace App\Controller;

use Core\AbstractInterface\AbstractController;

/**
 * 默认显示控制器
 * Class Index
 * @package App\Controller
 */
class Index extends AbstractController
{

    /**
     * 默认显示页面
     * @return mixed|void
     */
    public function index()
    {
        $this->response()->write('
            <style type="text/css">
                *{ padding: 0; margin: 0; }         
                div{ padding: 4px 48px;} 
                body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} 
                h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } 
                p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}
            </style>
            <div style="padding: 24px 48px;">
                <h1>:)</h1>
                <p>欢迎使用<b> easyPHP</b></p>
                <br/>
            </div>
         ');
    }

    /**
     * 前置函数
     * @param $actionName
     * @return mixed|void
     */
    public function onRequest($actionName)
    {
    }

    /**
     * 方法不存在的时候
     * @param null $actionName
     * @param null $arguments
     * @return mixed|void
     */
    public function actionNotFound($actionName = null, $arguments = null)
    {
    }

    /**
     * 后置函数
     * @return mixed|void
     */
    public function afterAction()
    {
    }
}