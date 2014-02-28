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

namespace Seaf\Logger;

use Seaf\Collection\ArrayCollection;

/**
 * ログハンドルコレクション
 */
class LogHandlerCollection extends ArrayCollection
{
    public function set( LogHandler $logHandler )
    {
        parent::set(
            spl_object_hash($logHandler),
            $logHandler
        );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
