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

namespace Seaf\Core\Factory;

/**
 * コールバックのファクトリクラス
 */
class FactoryClassName extends Factory
{
    /**
     * インスタンスを生成する
     */
    protected function createInstance( )
    {
        return DispatchHelper::dispatch( $this->context, $array = array());
    }
}
