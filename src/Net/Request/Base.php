<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Net\Request;

use Seaf\Data\Container;
use Seaf\Data;
use Seaf\Exception\Exception;

/**
 * BaseClass
 */
class Base extends Container\Base
{
    /**
     * イニシャライザ
     *
     * @var Initializer
     */
    protected $initializer;
    protected $initializer_name;

    /**
     * @var aray
     */
    protected $params;

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var string
     */
    protected $base_uri;

    public $_id = 1;

    /**
     * @param string|Initializer|null Inializerのクラス名か実体を指定する
     */
    public function __construct ($initializer = null)
    {
        if ($initializer == null) $initializer = $this->initializer_name;
        if (is_string($initializer)) {
            if (!class_exists($initializer)) {
                $initializer = __NAMESPACE__.'\\Initializer\\'.ucfirst($initializer);
            }
            $initializer = new $initializer($this);
        }
        $this->initializer = $initializer;
        $this->initRequest( );
    }

    /**
     * リクエストを初期化する
     */
    public function initRequest ()
    {
        $this->initializer->init();
    }

    /**
     * URLを取得する
     */
    public function getUri( )
    {
        return $this->uri->setMask($this->base_uri);
    }

    /**
     * ベースURLを取得する
     */
    public function getBaseUri( )
    {
        return $this->base_uri;
    }


    /**
     * ベースURLを追加する
     */
    public function addBasePath ($path)
    {
        if ($this->base_uri != '/' && $this->base_uri != '') {
            $this->base_uri = '/'.trim($this->base_uri,'/').'/'.trim($path,'/');
        } else {
            $this->base_uri = '/'.trim($path,'/');
        }
    }

    /**
     * パスを追加する
     */
    public function setPath ($path)
    {
        $this->uri->setPath($path);
    }

    /**
     * メソッドを取得する
     */
    public function getMethod( )
    {
        return $this->method;
    }

    public function __get ($name)
    {
        if (method_exists($this, $method = 'get'.$name)) {
            return $this->$method();
        }
        throw new Exception(array("%sはアクセスできないプロパティです", $name));
    }

    public function __toString ( )
    {
        return sprintf('%s %s',$this->getMethod(), $this->getUri());
    }

    public function __clone ( )
    {
        $this->uri = clone $this->uri;
        $this->_id++;
    }

    public function getHelper ( ) 
    {
        return Data\Helper::factory($this->data);
    }
}
