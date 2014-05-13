<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

/**
 * コマンド実行結果用のインターフェイス
 */
interface ResultIF
{
    /**
     * 戻り値を追加する
     */
    public function addReturnValue($value);

    /**
     * 戻り値を取得する
     */
    public function fetchReturnValue( );

    /**
     * エラーを通知する
     */
    public function error($code, $params = []);

    /**
     * エラー判定
     */
    public function isError( );
}
