<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/4/29
 * Time: 下午1:54
 */

namespace Core\Component\Spl;


/**
 * 标准的Bean类
 * Class SplBean
 * @package Core\Component\Spl
 */
abstract class SplBean implements \JsonSerializable
{
    /**
     * @var array
     */
    private $__varList = array();

    /**
     * SplBean constructor.
     * @param array $beanArray
     */
    final function __construct($beanArray = array())
    {
        $this->__varList = $this->allVarKeys();
        $this->initialize();
        $this->arrayToBean($beanArray);
    }

    /**
     * @param $property
     * @param $val
     * @return $this
     */
    final protected function setDefault(&$property, $val)
    {
        $property = $val;
        return $this;
    }

    /**
     * @return array|mixed
     */
    final function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        $data = array();
        foreach ($this->__varList as $var) {
            $data[$var] = $this->$var;
        }
        return $data;
    }

    /**
     * @return string
     */
    function __toString()
    {
        // TODO: Implement __toString() method.
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return mixed
     */
    abstract protected function initialize();

    /**
     * @return array
     */
    private function allVarKeys()
    {
        $data = get_class_vars(static::class);
        unset($data['__varList']);
        return array_keys($data);
    }

    /**
     * @param array|null $columns
     * @param bool $notNull
     * @return array|mixed
     */
    public function toArray(array $columns = null, $notNull = false)
    {
        if ($columns) {
            $data = $this->jsonSerialize();
            $ret = array_intersect_key($data, array_flip($columns));
        } else {
            $ret = $this->jsonSerialize();
        }
        if ($notNull) {
            return array_filter($ret, function ($val) {
                return !is_null($val);
            });
        } else {
            return $ret;
        }
    }

    /**
     * @param array $data
     * @return $this
     */
    public function arrayToBean(array $data)
    {
        $data = array_intersect_key($data, array_flip($this->__varList));
        foreach ($data as $var => $val) {
            $this->$var = $val;
        }
        return $this;
    }
}