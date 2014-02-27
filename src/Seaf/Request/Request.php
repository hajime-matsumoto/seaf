<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * @copyright Copyright (2) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Request;

use Seaf\Util\ArrayHelper;

/**
 * リクエスト
 */
class Request
{
    /**
     * requested datas
     */
    private $params = array();

    /**
     * @var string
     */
    private $url_base;
    private $url;
    private $method;
    private $body;


    private $POST,$GET,$SERVER,$COOKIES,$FILES;

    public  function __construct( )
    {
        $this->POST   = $_POST;
        $this->GET    = $_GET;
        $this->SERVER = $_SERVER;
        $this->COOKIE = $_POST;
        $this->FILES  = $_FILES;
    }

    /**
     * Getter
     */
    public function __get($key) 
    {
        if( method_exists( $this, $method = 'get'.ucfirst($key) ) ) 
        {
            return call_user_func( array($this,$method), $key );
        }

        throw new \Exception( $key . ' dose not access ' );
        return null;
    }

    /**
     * Setter
     */
    public function __set($key, $value)
    {
        if( method_exists( $this, $method = 'set'.ucfirst($key) ) ) 
        {
            return call_user_func( array($this,$method), $value );
        }
        return null;
    }

    /**
     * @param string
     */
    public function setBase( $url )
    {
        $this->url_base = $url;
    }

    /**
     * @param string
     */
    public function setUrl( $url )
    {
        $this->url = $url;
    }

    /**
     * @param string
     */
    public function setMethod( $method )
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getBody( )
    {
        if(!empty($this->body) ) return $this->body;

        return $this->body = file_get_contents('php://input');
    }

    /**
     * @return string
     */
    public function getBase( )
    {
        if( empty($this->url_base) )
        {
            $script = ArrayHelper::get($this->SERVER, 'SCRIPT_NAME', false);
            if( false !== $script ) 
            {
                $this->url_base = dirname($script);
            }
        }

        if( $this->url_base == "/" )  return false;
        return $this->url_base;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if( empty($this->url) ) 
        {
            $this->url = ArrayHelper::get( $this->SERVER, 'REQUEST_URI', '/');
        }

        if($this->getBase() && strpos( $this->url, $this->getBase() ) === 0 )
        {
            $this->url = substr($this->url, strlen($this->getBase()) );
        }

        if( !$this->url) return "/";
        return $this->url;
    }

    public function getMethod()
    {
        if( !empty($this->method) ) return $this->method;

        if ( isset($this->SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ) 
        {
            return $this->SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }
        elseif (isset($this->params['_method'])) 
        {
            return $this->params['_method'];
        }
        return ArrayHelper::get($this->SERVER,'REQUEST_METHOD', 'GET');
    }
}
