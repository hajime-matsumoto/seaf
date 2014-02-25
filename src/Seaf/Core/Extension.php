<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラス定義
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;

use Seaf\Core\Base;
use Seaf\Core\Environment;
use Seaf\Seaf;
use Seaf\Util\AnnotationHelper;

/**
 * エクステンションの抽象クラス
 *
 * エクステンションはEnvironmentへ
 * メソッド,コンポーネント,フィルター,アクション
 * を追加する事が出来ます。
 *
 * クラスアノテーション @component name Classでオブジェクトを
 * バインドする。
 *
 * メソッドアノテーション @bind method_name を持つメソッドを
 * オートバインドする。
 *
 * メソッドアノテーション @bind method_name を持つメソッドを
 * オートバインドする。
 *
 * 自分自身への呼び出しはprefix名でバインドされる
 */
abstract class Extension
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var object
     */
    private $base;

    public function init( $prefix, Base $base )
    {
        // Environmentを保存
        $this->base = $base;

        $this->prefix = $prefix;
        Seaf::debug('%sが初期化されました。', get_class($this));

        $base->register( $prefix, function(){ return $this; } );

        // クラスアノテーションを解析する
        $anot =  AnnotationHelper::getClassAnnotation( $this );
        if(array_key_exists('component', $anot)) 
        {
            $comps = $anot['component'];
            if( !is_array($comps) ) $comps = array($comps);
            foreach( $comps as $comp )
            {
                if( preg_match('/\s*([^\s]*)\s*([^\s]*)/', $comp, $m)) 
                {
                    $name = $m[1];
                    $class = $m[2];
                    $base->register( $this->prefix($name), $class );
                }
            }
        }

        // メソッドアノテーションを解析する
        array_walk(
            AnnotationHelper::getMethodsAnnotation( $this ),
            function( $anot, $method ) use ($base) {
                // バインド対象のメソッドを探す
                if(array_key_exists('bind', $anot) && !empty($anot['bind'])) {
                    $method_name = $anot['bind'];

                    // @usePrefix が 文字列falseでない限り
                    // プレフィックスを有効にする
                    if( array_key_exists('usePrefix', $anot) && $anot !== 'false') {
                        $method_name = $this->prefix($method_name);
                    }
                    $base->mapMethod( $method_name, array( $this, $method ) );
                }
            }
        );

        $this->initExtension( );
    }

    /**
     * プレフィックスをかける
     *
     * @param string $name
     * @return string
     */
    private function prefix($name)
    {
        return $this->prefix.ucfirst($name);
    }

    /**
     * エクステンションは自分が登録した
     * オブジェクトをプレフィックスレスで取得できる。
     *
     * 見つからなければBaseオブジェクトの__getへフォワード
     */
    public function __get($name)
    {
        if( $this->base->hasComponent($this->prefix($name)) )
        {
            return $this->base->getComponent($this->prefix($name));
        }

        return $this->base->$name;
    }


    /**
     * エクステンションはbaseのメソッド群を透過的に実行できる。
     */
    public function __call( $name, $params )
    {
        if( in_array( $name, array('after','before') ) )
        {
            // プレフィックス付のフックを掛ける
            $params[0] = $this->prefix($params[0]);
        }
        // エクステンションプレフィックス付のメソッドが登録されていれば
        // それを優先して呼び出す。
        // 例： stop webStopが登録されていたとして、自分のエクステンション
        // プレフィックスがwebであれば、$this->stop としても$this->webStop
        // が呼ばれる。stopが使いたければ$this->base->stopとして実行する。
        elseif( is_callable( array($this->base, $this->prefix($name)) ) )
        {
            $name = $this->prefix($name);
        }

        return $this->base->__call($name, $params);
    }

    abstract protected function initExtension();
}
