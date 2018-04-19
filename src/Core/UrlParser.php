<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2016/5/9
 * Time: 16:21
 */

namespace Core;

use Core\Http\Request;
use Core\Http\Message\Uri;

/**
 * Class UrlParser
 * @package Core
 */
class UrlParser
{
    static public function pathInfo()
    {
        //  /kingofhua/php.git
        $pathInfo = Request::getInstance()->getUri()->getPath();

        //  /kingofhua
        $basePath = str_replace('\\', '/', dirname($pathInfo));//if in windows

        /**
         * array(
         *  [dirname] => /kingofhua
         *  [basename] => php.git
         *  [extension] => git
         *  [filename] => php
         * )
         */
        $info = pathInfo($pathInfo);

        if ($info['filename'] != 'index') {
            if ($basePath == '/') {
                $basePath = $basePath . $info['filename'];
            } else {
                $basePath = $basePath . '/' . $info['filename'];
            }
        }

        //  /kingofhua/php
        return $basePath;
    }
}