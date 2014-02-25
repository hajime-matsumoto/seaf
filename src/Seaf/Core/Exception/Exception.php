<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラス定義ファイル
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core\Exception;

/**
 * 例外のベースクラス
 */
class Exception extends \Exception
{
    /**
     * 例外メッセージをsprintf的に書ける
     */
    public function __construct( $string )
    {
        if( func_num_args() > 1 ) {
            $args = func_get_args();
            $args = array_slice($args,1);
            foreach( $args as $k=>$v )
            {
                if( is_object($v) )
                {
                    $args[$k] = get_class($v);
                }
                if( is_array($v) )
                {
                    $args[$k] = "keys:".implode(",", array_keys($v));
                }
            }
        }
        if( !empty($args) ) $string = vsprintf( $string, $args );

        parent::__construct( $string );
    }
}
