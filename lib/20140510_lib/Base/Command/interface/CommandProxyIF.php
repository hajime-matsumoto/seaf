<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

interface CommandProxyIF extends CommandIF
{
    // ========================================
    // セッター
    // ========================================

    /**
     * スコープを設定する
     * 
     * @param string
     */
    public function setScope($scope);

    /**
     * 名称を設定する
     * 
     * @param string
     */
    public function setName($name);

    /**
     * パラメタをセットする
     * 第一引数が配列ならsetParamsにフォワード
     * 
     * @param string|array
     * @param mixed
     */
    public function setParam ($name, $value = null);

    /**
     * パラメタを配列でセットする
     * 
     * @param array
     */
    public function setParams ($params);
}
