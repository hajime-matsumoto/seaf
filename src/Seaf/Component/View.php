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
use Seaf\DI\DIContainer;
use Seaf\View\View as SeafView;

/**
 * VIEWコンポーネント
 */
class View extends SeafView
{

    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        // オリジナルDIのregistryをコピーする
        $this->di('registry')->sync( $di->get('registry') );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
