<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Data\Helper;

use Seaf\Data;
use Seaf\Kernel\Kernel;
use IteratorAggregate;
use ArrayIterator;

class ArrayHelper extends Data\Helper implements IteratorAggregate
{
    /**
     * @var array
     */
    protected $datas = array();

    public function __construct (array $data, $name, $parent = null)
    {
        $this->init($name, $parent);

        foreach ($data as $k=>$v)
        {
            $this->datas[$k] = Data\Helper::factory($v, $this);
        }
    }

    public function has ($name)
    {
        return isset($this->datas[$name]);
    }

    public function isEmpty()
    {
        return empty($datas);
    }

    public function __get($name)
    {
        if (is_array($name)) {
            foreach ($name as $n) {
                $helper = $this->__get($n);
                if (!$helper->isEmpty()) return $helper;
            }
        } elseif (isset($this->datas[$name])) {
            return $this->datas[$name];
        }
        return new NullHelper($name, $this);
    }

    public function __invoke ($name, $default = null)
    {
        $helper = $this->__get($name);
        if ($helper instanceof NullHelper) {
            return Data\Helper::factory($default);
        }
        return $helper;
    }

    public function __call ($name, $params)
    {
        $helper = $this->__get($name);
        if ($helper instanceof NullHelper) {
            return $helper;
        }
        return Kernel::dispatcher($helper, $params, $this)->dispatch();
    }

    public function getIterator ( )
    {
        return new ArrayIterator($this->datas);
    }
}
