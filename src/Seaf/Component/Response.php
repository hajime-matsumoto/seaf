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
 * RESPONSEコンポーネント
 *
 *
 * チェインパターンにする
 */
class Response
{
    /**
     * ステータスコードリスト
     */
    public static $codes = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ); 

    /**
     * レスポンス：ボディ
     */
    protected $body;

    /**
     * レスポンス：ステータス
     */
    protected $status = 200;


    public function __construct( )
    {
        $this->reset();
    }

    /**
     * ステータスをセットする
     */
    public function status( $code = 200 )
    {
        $this->status = $code;
        return $this;
    }

    /**
     * リセットする
     */
    public function reset( )
    {
        $this->body = "";
        return $this;
    }


    /**
     * 書き込む
     */
    public function write($body)
    {
        $this->body .= $body;
        return $this;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
