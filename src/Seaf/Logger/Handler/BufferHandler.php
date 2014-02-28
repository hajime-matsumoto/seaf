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


/**
 * ログハンドラ
 */
class BufferHandler extends Handler
{
    private $logs = array();

    /**
     * スクリプト-開始
     */
    public function waikup( )
    {
        $this->logs = array();
    }

    /**
     * ログ送出
     */
    protected function _post( $message )
    {
        $this->logs[] = $message;
    }

    /**
     * スクリプト-終了
     */
    public function shutdown( )
    {
        echo "=== LOGS ===\n";
        foreach($this->logs as $log )
        {
            echo $log."\n";
        }
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
