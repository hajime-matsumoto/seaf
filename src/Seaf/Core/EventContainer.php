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

namespace Seaf\Core;
use Seaf\Util\DispatchHelper;

/**
 * イベントコンテナ
 *
 * イベントを保持するクラス
 */
class EventContainer extends Container
{
    /**
     * フックコンテナ
     */
    private $hookContainer;

    /**
     * イベントを保持する
     */
    public function __construct( )
    {
        $this->hookContainer = new Container( );
    }

    /**
     * フックの登録
     *
     * @param string $key
     * @param callback $callbaek
     */
    public function addHook( $name, $callback = null )
    {
        $this->hookContainer->store( $name, $callback, $push = true );
    }

    /**
     * イベントの実行
     */
    public function trigger( $name, $params )
    {
        if( $this->hookContainer->has( $name ) )
        {
            foreach( $this->hookContainer->restoreMulti( $name ) as $event )
            {
                $continue = DispatchHelper::dispatch($event, $params);
            }
        }
    }
}
