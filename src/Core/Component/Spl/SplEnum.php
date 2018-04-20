<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/11
 * Time: 下午2:56
 */

namespace Core\Component\Spl;


/**
 * 标准的Enum 模版？
 * Class SplEnum
 * @package Core\Component\Spl
 */
class SplEnum
{
    /**
     *
     */
    const __default = null;
    /**
     * @var false|int|string
     */
    private $selfEnum;

    /**
     * SplEnum constructor.
     * @param $enumVal
     */
    final function __construct($enumVal)
    {
        $list = static::enumList();
        $key = array_search($enumVal, $list, true);
        $this->selfEnum = $key ? $key : '__default';
    }

    /**
     * @param $val
     * @return bool
     */
    final function equals($val)
    {
        $list = static::enumList();
        return $list[$this->selfEnum] === $val ? true : false;
    }

    /**
     * @param $enumVal
     * @return bool|false|int|string
     */
    static function inEnum($enumVal)
    {
        $list = static::enumList();
        $key = array_search($enumVal, $list, true);
        return $key ? $key : false;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    final static function enumList()
    {
        $ref = new \ReflectionClass(static::class);
        return $ref->getConstants();
    }

    /**
     * @return mixed|string
     * @throws \ReflectionException
     */
    final function __toString()
    {
        // TODO: Implement __toString() method.
        $list = static::enumList();
        $data = $list[$this->selfEnum];
        if (is_string($data)) {
            return $data;
        } else {
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return static
     * @throws \ReflectionException
     */
    final  static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        $list = static::enumList();
        $val = isset($list[$name]) ? $list[$name] : null;
        return new static($val);
    }
}