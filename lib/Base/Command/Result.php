<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

/**
 * 
 */
class Result implements ResultIF
{
    private $error;
    private $returnValue;

    public function setReturnValue ($value)
    {
        $this->returnValue = $value;
    }

    public function getReturnValue ( )
    {
        return $this->returnValue;
    }

    public function error($name, $params = array())
    {
        $this->error = [$name, $params];
        return $this;
    }

    public function isError( )
    {
        return empty($this->error) ? false: true;
    }

    public function returnValue($value)
    {
        $this->setReturnValue($value);
        return $this;
    }
}
