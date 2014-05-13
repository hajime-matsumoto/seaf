<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\BackEnd;

/**
 * WEBコンポーネントのベースクラス
 */
trait WebComponentTrait
{
    use BackEnd\Module\ModuleFacadeTrait;

    /**
     * 初期化
     */
    public function initWebComponent( )
    {
        $this->initFacade( );
    }

    /**
     * ウェブコンポーネントの親を追加する
     */
    protected function setWebComponentParent(WebComponentIF $component)
    {
        $this->setParent($component);
        $this->addObserver($component);
    }

    /**
     * ウェブコンポーネントの親を探す
     */
    protected function getWebComponentParent( )
    {
        if ($this->hasParent()) {
            $parent = $this->getParent();
            if ($parent instanceof WebComponentIF) {
                return $parent;
            }
        }
        return false;
    }

    /**
     * ウェブコンポーネントのルートを探す
     */
    protected function getWebComponentRoot( )
    {
        if ($parent = $this->getWebComponentParent( ))
        {
            return $parent->getWebComponentRoot( );
        }
        return $this;
    }

}
