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
Interface WebComponentIF extends BackEnd\Module\ModuleFacadeIF
{
    /**
     * ウェブコンポーネントの親を追加する
     */
    public function setWebComponentParent(WebComponentIF $component);

    /**
     * ウェブコンポーネントの親を探す
     */
    public function getWebComponentParent( );

    /**
     * ウェブコンポーネントのルートを探す
     */
    public function getWebComponentRoot( );
}
