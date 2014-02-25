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

        // メンバ変数に主要なオブジェクトを登録
        $this->request  = $this->web()->request;
        $this->response = $this->web()->response;
        $this->router   = $this->web()->router;

        // アノテーションを取得
        $annotations = AnnotationHelper::getMethodsAnnotation( $this );

        // アノテーションへの処理
        array_walk( $annotations, function( $anot, $method ) {

            // @hookがあればフィルタに登録する
            if( array_key_exists('hook',$anot) ) {
                if( preg_match("/\s*([^\s]*)\s*([^\s]*)/", $anot['hook'], $m) )
                {
                    $this->web()->addHook($m[1].'.'.$m[2], array($this,$method));
                }
            }
            // @routeがあればルーティングする
            if( array_key_exists('route',$anot) ) {
                if( array_key_exists('method',$anot) ) {
                    $pattern = sprintf("%s %s",$anot['method'],$anot['route']);
                }else{
                    $pattern = $anot['route'];
                }
                $this->web()->route( $pattern, array($this,$method) );
            }
        });

        $this->initWebApp();
    }

}
