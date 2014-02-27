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


    public function initializeExtension( $prefix, Environment $env )
    {
        // Environmentを保存
        $this->base = $env->get('base');

        $this->prefix = $prefix;

        $this->base->set('ext.'.$prefix,$this);
        Seaf::debug('%sが初期化されました。', get_class($this));
    }

    /**
     * プレフィックスをかける
     *
     * @param string $name
     * @return string
     */
    protected function prefix($name)
    {
        return $this->prefix.ucfirst($name);
    }

    /**
     * onをオーバーライド
     */
    public function on( $name, $func )
    {
        $this->base->on($this->prefix($name), $func);
    }

    /**
     * triggerをオーバーライド
     */
    public function trigger( $name )
    {
        $this->base->trigger($this->prefix($name) );
    }

    /**
     * エクステンションは自分が登録した
     * オブジェクトをプレフィックスレスで取得できる。
     *
     * 見つからなければBaseオブジェクトの__getへフォワード
     */
    public function __get($name)
    {
        if( $this->base->isRegistered($this->prefix($name)) )
        {
            return $this->base->retrieve($this->prefix($name));
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
        elseif( $this->base->isMaped( $this->prefix($name))  )
        {
            $name = $this->prefix($name);
        }

        return $this->base->__call($name, $params);
    }

    //abstract protected function initExtension();
}
