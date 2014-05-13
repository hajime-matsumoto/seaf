<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

use Seaf\Util\Util;

/**
 *  キューコンテナ
 */
class CueContainer
{
    private $data = [];
    private $sep;

    /**
     * コンストラクタ
     */
    public function __construct ($array = [], $sep = ',')
    {
        $this->sep = $sep;

        if (is_string($array)) {
            $array = [$array];
        }
        $this->add($array);
    }

    public function add($value)
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->add($v);
            }
        }else{
            $parts = explode($this->sep, $value);
            if (count($parts) > 1) {
                $this->add($parts);
            }else{
                $this->data[] = ucfirst($value);
            }
        }
    }

    public function prepend($value)
    {
        array_unshift($this->data, $value);
    }

    public function append($value)
    {
        array_push($this->data, $value);
    }

    public function first( )
    {
        return current($this->data);
    }

    public function shift( )
    {
        return array_shift($this->data);
    }

    public function pop( )
    {
        return array_pop($this->data);
    }

    public function isEmpty( )
    {
        return empty($this->data);
    }

    public function dump ( )
    {
        Util::dump($this);
    }

    public function toArray ( )
    {
        return $this->data;
    }

    public function __toString ( )
    {
        return (string) implode($this->sep, $this->data);
    }
}
