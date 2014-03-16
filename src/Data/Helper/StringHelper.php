<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Data\Helper;

use Seaf\Data;

class StringHelper extends Data\Helper
{
    /**
     * @var string
     */
    private $string = "";

    public function __construct ($string, $name, $parent = null)
    {
        $this->init($name, $parent);

        $this->string = $string;
    }

    public function toString( $default = "" )
    {
        if (empty($this->string)) return $default;
        return $this->string;
    }

    public function not ($str, $format_true = "%s", $format_false = "%s")
    {
        $format = ($this->string != $str) ? $format_true: $format_false;
        return new self(sprintf($format, $this->string), $this);
    }

    public function regex ($regex, $true = true, $false = false)
    {
        return preg_match($regex, $this->string) ?
            $true:
            $false;
    }

    public function eq ($string)
    {
        return $string == $this->string;
    }

    public function isEmpty( )
    {
        return empty($this->string);
    }

    public function __invoke ( )
    {
        return $this->string;
    }
}
