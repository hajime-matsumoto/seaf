<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * 型ライブラリ
 */
namespace Seaf\Base\Types;

/**
 * クラス名型
 */
class ClassNameString extends SeparatedString
{
    private $data = [];

    public function __construct($default = [])
    {
        parent::__construct('\\', $default);
    }


    public function newInstance ( )
    {
        return $this->newInstanceArgs(func_get_args());
    }

    public function newInstanceArgs ($args = [])
    {
        $class = $this->__toString();
        /*
        if (!class_exists($class)) {
            throw new \Exception( sprintf(
                'Class Not Exists >>> %s <<<',
                $class
            ));
        }
         */
        $rc = new \ReflectionClass($this->__toString());
        return $rc->newInstanceArgs($args);
    }

    public function filterParts($v)
    {
        return ucfirst($v);
    }

}
