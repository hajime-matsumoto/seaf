<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Net\Request;

use Seaf\Net\Request;
use Seaf\Kernel\Kernel;
use Seaf\Data;
use Seaf\Pattern\Configure;

/**
 * URI
 */
class Uri
{
    use Configure;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $uri_mask;

    /**
     * @var string
     */
    private $uri_parts = array();

    /**
     *
     */
    public static function factory ($config)
    {
        $uri = new self($config);
        return $uri;
    }

    public function __construct ($config = array())
    {
        $this->configure($config);
    }

    /**
     * URIをセットする
     *
     * @param string
     */
    public function setUri ($uri)
    {
        $this->uri = $uri;
        $this->uri_parts = parse_url($uri);
    }

    /**
     * パスをセットする
     *
     * @param string
     */
    public function setPath ($path)
    {
        $this->uri_parts['path'] = $path;
    }

    /**
     * URIマスクをセットする
     *
     * @param string
     */
    public function setMask ($uri)
    {
        $this->uri_mask = $uri;
        return $this;
    }

    /**
     * GetAbs
     */
    public function getAbs ($path)
    {
        return sprintf(
            "/%s/%s",
            trim($this->uri_mask, '/'),
            trim($path, '/')
        );
    }

    /**
     * @param string
     */
    public function toString ( )
    {
        $path = (string) Data\Helper::factory($this->uri_parts)->path();
        $mask = (string) $this->uri_mask;
        if ($mask != '/' && !empty($mask) && 0 === strpos($path, $mask)) {
            $path = substr($path,strlen($mask));
            if ($path == '') return '/';
        }
        return $path;
    }

    public function __toString ( )
    {
        return (string) $this->toString( );
    }
}
