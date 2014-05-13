<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * 型ライブラリ
 */
namespace Seaf\Base\Types;

/**
 * セパレータ付文字列
 */
class SeparatedString
{
    private $data = [];
    private $sep;
    private $output_sep;

    public function __construct($sep = ",", $default = null, $output_sep = null)
    {
        if (!empty($default)) {
            $this->init($default);
        }
        $this->sep = $sep;
        $this->output_sep = (empty($output_sep)) ? $this->sep: $output_sep;

    }

    public function isEmpty( )
    {
        return empty($this->data);
    }

    public function init($data)
    {
        $this->data = [];
        $this->add($data);
    }

    public function add($name)
    {
        if (empty($name)) {
            return;
        }
        if (is_array($name)) {
            foreach ($name as $v) $this->add($v);
            return $this;
        }

        if (false !== strpos($name, $this->sep)) {
            foreach (explode($this->sep, $name) as $v) {
                $this->add($v);
            }
            return $this;
        }
        return array_push($this->data, $name);
    }

    public function filterParts($v)
    {
        return $v;
    }

    public function last( )
    {
        return $this->data[count($this->data)-1];
    }

    public function __toString( )
    {
        array_walk($this->data, function(&$v) {
            $v = $this->filterParts($v);
        });
        return (string) implode($this->output_sep, $this->data);
    }
}
