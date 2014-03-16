<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Data;

class Helper
{
    protected $parent;
    protected $name;

    public static function factory ($data, $name = null, $parent = null)
    {
        if ($data instanceof self) {
            return $data;
        } elseif (is_string($data)) {
            return new Helper\StringHelper($data, $name, $parent);
        } elseif (is_int($data)) {
            return new Helper\IntHelper($data, $name, $parent);
        } elseif (is_array($data)) {
            return new Helper\ArrayHelper($data, $name, $parent);
        } elseif (is_object($data)) {
            return new Helper\ObjectHelper($data, $name, $parent);
        } elseif (empty($data)) {
            return new Helper\NullHelper($name, $parent);
        }
    }

    public function init ($name, $parent)
    {
        $this->name = $name;
        //$this->parent = $parent;
    }

    public function __toString ()
    {
        return $this->toString();
    }

    public function getOr($default)
    {
        return $default;
    }

    public function toString( $default = '' )
    {
        return $default;
    }

    public function isEmpty( )
    {
        return true;
    }

    public function regex ($regex, $true = true, $false = false)
    {
        return $false;
    }

    public function not ($str, $true = true, $false = false)
    {
        return $true;
    }

    public function __call ($name, $params)
    {
        Kernel::logger()->warning(array("%sは実装されてません", $name));
        return false;
    }


}
