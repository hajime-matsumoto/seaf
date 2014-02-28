<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * 例外クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Exception;

use Exception as PHPException;

/**
 * 例外クラス
 */
class Exception extends PHPException
{

    /**
     * メッセージを必須にする
     *
     * @param string $message メッセージかメッセージフォーマット
     * @param mixed $v....
     */
    public function __construct( $message )
    {
        if( func_num_args() > 1 )
        {
            $message = vsprintf( $message,
                array_slice(
                    func_get_args(), 1
                )
            );
        }

        parent::__construct( $message );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
