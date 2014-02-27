<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラス定義ファイル
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;

use Seaf\Util\DispatchHelper;

/**
 * メソッドコンテナ
 *
 * メソッドを保持するクラス
 */
class MethodContainer extends Container
{
    /**
     * ヘルパを実行する
     */
    public function invoke( $name )
    {
        $func = $this->restore( $name );
        return DispatchHelper::invokeArgs( $func, array_slice(func_get_args(),1) );
    }

    /**
     * 引数固定でヘルパを実行
     */
    public function invokeArgs( $name, $args)
    {
        return DispatchHelper::invokeMethodArgs( $this, 'invoke', array($name, $args) );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: */
