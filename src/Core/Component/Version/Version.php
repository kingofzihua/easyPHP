<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/7/7
 * Time: 下午2:30
 */

namespace Core\Component\Version;


/**
 * 版本控制
 * Class Version
 * @package Core\Component\Version
 */
class Version
{
    /**
     * @var array
     */
    private $maps = array();
    /**
     * @var
     */
    private $defaultHandler;

    /**
     * @param $rowPathInfo
     * @param $targetPathOrClosureHandler
     * @return $this
     */
    function addPathMap($rowPathInfo, $targetPathOrClosureHandler)
    {
        $this->maps[$rowPathInfo] = $targetPathOrClosureHandler;
        return $this;
    }

    /**
     * 设置默认的处理方法
     * @param $defaultPathOrClosureHandler
     * @return $this
     */
    function setDefaultHandler($defaultPathOrClosureHandler)
    {
        $this->defaultHandler = $defaultPathOrClosureHandler;
        return $this;
    }

    /**
     * @return array
     */
    public function getPathMaps()
    {
        return $this->maps;
    }

    /**
     * @param $rowPath
     * @return mixed|null
     */
    public function getPathMap($rowPath)
    {
        if (isset($this->maps[$rowPath])) {
            return $this->maps[$rowPath];
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getDefaultHandler()
    {
        return $this->defaultHandler;
    }


}