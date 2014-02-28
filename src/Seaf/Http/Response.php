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

namespace Seaf\Http;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Collection\ArrayCollection;
use Seaf\Component\Response as ResponseComponent;

/**
 * HTTP用 RESPONSEコンポーネント
 */
class Response extends ResponseComponent
{
    /**
     * HTTPヘッダー
     */
    private $headers = array();

    /**
     * リセット時にヘッダもリセットしてもらう
     */
    public function reset( )
    {
        $this->headers = array();
        parent::reset();

        return $this;
    }

    /**
     * HTTPヘッダーをセットする
     */
    public function header( $name, $value = null )
    {
        if(is_array($name)) 
        {
            foreach($name as $k=>$v) $this->header($k,$v);
        }
        else
        {
            $this->headers[$name] = $value;
        }
        return $this;
    }

    /**
     * キャッシュヘッダ
     */
    public function cache( $expires )
    {
        if ($expires === false) {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            );
            $this->headers['Pragma'] = 'no-cache';
        }
        else {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            $this->headers['Cache-Control'] = 'max-age='.($expires - time());
        }

        return $this;
    }

    /**
     * ヘッダを送信する
     *
     * @return object Self reference
     */
    public function sendHeaders() {
        // Send status code header
        if (strpos(php_sapi_name(), 'cgi') !== false) {
            $this->sendheader(
                sprintf(
                    'Status: %d %s',
                    $this->status,
                    self::$codes[$this->status]
                ),
                true
            );
        }
        else {
            $this->sendheader(
                sprintf(
                    '%s %d %s',
                    (
                        isset($_SERVER['SERVER_PROTOCOL']) ?
                        $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'
                    ),
                    $this->status,
                    self::$codes[$this->status]
                ),
                true,
                $this->status
            );
        }

        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $this->sendheader($field.': '.$v, false);
                }
            }
            else {
                $this->sendheader($field.': '.$value);
            }
        }

        return $this;
    }

    /**
     * ヘッダー
     */
    public function sendHeader( $header )
    {
        Seaf::di('system')->sendHeader( $header );
    }


    /**
     * 送信
     */
    public function send( )
    {
        if( ob_get_length() > 0 )
        {
            ob_end_clean();
        }

        if( !headers_sent( ) )
        {
            $this->sendheaders();
        }

        Seaf::di('system')->halt( $this->body );
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
