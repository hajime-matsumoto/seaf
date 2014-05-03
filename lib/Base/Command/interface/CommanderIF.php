<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

/**
 * 司令官インターフェイス
 */
interface CommanderIF
{
    /**
     * 新規リクエストを生成する
     *
     * @return Seaf\Base\Command\Request
     */
    public function newRequest ( );

    /**
     * リクエストを受け付ける
     *
     * @param Seaf\Base\Command\Request
     */
    public function recieveRequest(Request $request);
}

