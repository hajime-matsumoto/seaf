<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Net\Request;

class Initializer extends Base
{
    /**
     * @var Base
     */
    protected $base;

    public function __construct (Base $base)
    {
        $this->base = $base;
    }

    /**
     * 環境ごとに違うイニシャライズはここに格納
     */
    public function init ()
    {
        
    }
}
