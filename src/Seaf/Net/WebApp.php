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

        $this->_init( $root, $env);
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
    public function init( )
    {
    }

    private function _init( $root, $env )
    {
        parent::init( $root, $env);

        // web機能を有効にする
        $this->enable('web');
        $this->web  = $this->exten('web');
        $this->request = $this->web->request;
        $this->response = $this->web->response;

        // 初期化処理
        $this->init();

        // アノテーションを取得
        $annotations = AnnotationHelper::getMethodsAnnotation( $this );

        // アノテーションへの処理
        array_walk( $annotations, function( $anot, $method ) {

            // @afterがあればフィルタに登録する
            if( array_key_exists('after',$anot) ) {
                $this->web->after( $anot['after'], array($this,$method ));
            }
            // @beforeがあればフィルタに登録する
            if( array_key_exists('before',$anot) ) {
                $this->web->before( $anot['before'], array($this,$method ));
            }
            // @routeがあればルーティングする
            if( array_key_exists('route',$anot) ) {
                if( array_key_exists('method',$anot) ) {
                    $pattern = sprintf("%s %s",$anot['method'],$anot['route']);
                }else{
                    $pattern = $anot['route'];
                }
                $this->web->route( $pattern, array($this,$method) );
            }
        });
    }

}
