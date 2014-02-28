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

namespace Seaf\Logger\Handler;

use Seaf\Logger\Logger;
/**
 * ログハンドラ
 */
abstract class Handler
{
    private $name = 'Logger';
    private $format = '[%date%] %name%.%level%: %message%';
    private $date_format = 'Y-m-d H:i:s';
    private $compiled_format;

    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * ログ送出
     */
    abstract protected function _post( $message );

    /**
     * ログ送出
     */
    public function post( $message, $level )
    {
        $order = array(
            '%1$s' => '%date%',
            '%2$s' => '%name%',
            '%3$s' => '%level%',
            '%4$s' => '%message%'
        );

        if( empty($this->compiled_format) )
        {
            $this->compiled_format = str_replace(
                $order,
                array_keys($order),
                $this->format
            );
        }

        $message = vsprintf($this->compiled_format,array(
            date($this->date_format),
            $this->name,
            Logger::$error_map[$level],
            $message
        ));

        $this->_post($message);
    }

    /**
     * スクリプト-開始
     */
    public function waikup( )
    {
    }

    /**
     * スクリプト-終了
     */
    public function shutdown( )
    {
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
