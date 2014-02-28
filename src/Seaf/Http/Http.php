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

namespace Seaf\Http;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Http\Http as SeafHTTP;

/**
 * HTTPコンポーネント
 */
class Http extends Base
{
    public function __construct( )
    {
        parent::__construct();

        $this->di('registry')->set('name', 'Http');

        $this->di()->factory()->register('request',__NAMESPACE__.'\\Request');
        $this->di()->factory()->register('response',__NAMESPACE__.'\\Response');

        // ヘルパをマップする
        $this->di('helperHandler')->bind( $this, array(
            'run'=>'_run',
            'stop'=>'_stop',
            'halt'=>'_halt',
            'notfound'=>'_notfound'
        ));
    }

    /**
     * 実行する
     */
    public function _run( )
    {
        Seaf::debug(get_class($this).'::run 開始');

        $is_found = false;

        if( ob_get_length() > 0 )
        {
            ob_end_clean();
        }

        ob_start();

        $this->event()->trigger('before.start');

        $this->event()->addHook('after.start',function(){
            $this->stop();
        });

        while($route = $this->router()->route($this->request()))
        {
            $callback = $route->getCallback();

            // ルートしたコールバックの戻り値が真なら
            // 次にマッチするルートを探しに行く
            $to_continue = $callback();

            $is_found = true;
            if( !$to_continue ) break;

            $this->router()->next();
        }


        if( $is_found  === false) $this->notfound();


        $this->event()->trigger('after.start');
    }

    /**
     * 強制終了
     */
    public function _halt($code = 200, $message = '')
    {
        $this->response( )
            ->reset( )
            ->status( $code )
            ->write($message)
            ->send( );
    }


    /**
     * NotFoundを実行する
     */
    public function _notfound( )
    {
        $this->response( )
            ->reset( )
            ->status(404)
            ->write('<h1>404 NOT FOUND</h1>')
            ->send( );
    }

    /**
     * 終了処理
     */
    public function _stop( )
    {
        $this->event()->trigger('before.stop');

        Seaf::debug(get_class($this).'::run 終了');
        $this->response( )
            ->reset( )
            ->status(200)
            ->write(ob_get_clean())
            ->send( );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
