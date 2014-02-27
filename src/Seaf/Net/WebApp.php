<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Web App Class
 *
 * 継承したクラスで使えるアノテーション
 *
 * フィルター
 * - @after start
 * - @before start
 *
 * ルーティング
 * - @map start
 * - @method start
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Net;

use Seaf\Core\Base;
use Seaf\Util\AnnotationHelper;


class WebApp extends Base
{
    protected $web;
    protected $request;
    protected $response;

    public function __construct( $root, $env = 'development' )
    {
        parent::__construct( );

        $this->set('app.root', $root);
        $this->set('app.env', $env);

        $this->init();
    }

    /**
     * Web処理をスタート
     */
    public function run( )
    {
        $this->webStart();
    }

    /**
     * Web初期化処理
     */
    public function initWebApp( )
    {
    }

    private function init( )
    {
        // web機能を有効にする
        $this->useExtension('web');

        $web = $this->web = $this->get('ext.web');

        // メンバ変数に主要なオブジェクトを登録
        $this->request  = $web->request;
        $this->response = $web->response;
        $this->router   = $web->router;

        // アノテーションを取得
        $anot = AnnotationHelper::get( $this );

        foreach($anot->getMethodAnnotation() as $method=>$anot)
        {
            if( array_key_exists('SeafURL', $anot) )
            {
                if( array_key_exists('SeafMethod', $anot) )
                {
                    $anot['SeafURL'] = $anot['SeafMethod'].' '.$anot['SeafURL'];
                }
                $web->route($anot['SeafURL'], array($this,$method));
            }

            if( array_key_exists('SeafHookOn', $anot) )
            {
                list( $target, $when ) = preg_split('/\s+/', $anot['SeafHookOn'], 2);
                $web->on( $target.'.'.$when, array($this,$method));
            }
        }


        $this->initWebApp();
    }

}
