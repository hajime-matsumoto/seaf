<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Component;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Collection\ArrayCollection;

/**
 * REQUESTコンポーネント
 */
class Request extends ArrayCollection
{
    private $URL = false;
    private $baseURL = '';
    private $originalURL;
    private $method = 'GET';

    public function __construct( )
    {
    }

    /**
     * ベースURLを設定する
     * ・設定されたらURLを初期化する
     *
     * @param string $url
     */
    public function setBaseURL( $url )
    {
        $this->baseURL = $url;
        $this->URL = false;
    }

    /**
     * ベースURLを取得する
     *
     * @return string $url
     */
    public function getBaseURL( )
    {
        return $this->baseURL;
    }

    /**
     * リクエストされたURLを返す
     *
     * @param string $url
     */
    public function getURL()
    {
        if( $this->URL == false )
        {
            $this->URL = $this->_getURL();
            $doRemoveBaseURL =
                $this->baseURL != '/' &&
                strlen($this->baseURL) > 0 &&
                strpos($this->URL,$this->baseURL) === 0;
            if( $doRemoveBaseURL )
            {
                $this->URL = substr($this->URL, strlen($this->baseURL));
            }
        }

        $this->URL = empty($this->URL) ? '/': $this->URL;

        return $this->URL;
    }

    /**
     * リクエストされたURLを返す
     *
     * @param string $url
     */
    protected function _getURL( )
    {
        return $this->originalURL;
    }

    /**
     * リクエストURLをセットする
     */
    public function setURL( $url )
    {
        $params = array();
        $args =  parse_url( $url ) ;
        if( isset($args['query']) ) {
            parse_str($args['query'],$params);
        }

        foreach( $params as $k=>$v )
        {
            $this->set($k,$v);
        }
        $this->originalURL = $args['path'];
    }

    /**
     * メソッドをセットする
     */
    public function setMethod( $method )
    {
        $this->method = $method;
    }

    /**
     * メソッドを取得する
     */
    public function getMethod(  )
    {
        return $this->method;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
