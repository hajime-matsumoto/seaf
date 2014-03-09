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

namespace Seaf\Web\Component;

use Seaf;
use Seaf\FrameWork\Component as FWComp;

/**
 * レスポンスクラス
 */
class Response extends FWComp\Response
{
    public function sendHeaders () {
        // Send status code header
        if (strpos(php_sapi_name(), 'cgi') !== false) {
            Seaf::system()->header(
                sprintf(
                    'Status: %d %s',
                    $this->status,
                    self::$codes[$this->status]
                ),
                true
            );
        } else {
            Seaf::system()->header(
                sprintf(
                    '%s %d %s',
                    (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                    $this->status,
                    self::$codes[$this->status]),
                true,
                $this->status
            );
        }


        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    Seaf::system()->header($field.': '.$v, false);
                }
            } else {
                Seaf::system()->header($field.': '.$value);
            }
        }

        return $this;
    }

    /**
     * Sets caching headers for the response.
     *
     * @param int|string $expires Expiration time
     * @return object Self reference
     */
    public function cache($expires) {
        if ($expires === false) {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            );
            $this->headers['Pragma'] = 'no-cache';
            $this->headers['X-Accel-Expires'] = $expires;
        }
        else {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            $this->headers['Cache-Control'] = 'max-age='.($expires - time());
            $this->headers['Pragma'] = 'cache';
            $this->headers['X-Accel-Expires'] = 0;
        }

        return $this;
    }

    public function send ( )
    {
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            $this->sendHeaders();
        }

        Seaf::system()->halt($this->body);
    }

    public function sendJson( )
    {
        $json = $this->toJson( );

        $this->init();
        $this->header('Content-Type', 'application/json')->write($json)->send();
    }

}
