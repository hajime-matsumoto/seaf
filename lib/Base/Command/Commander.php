<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

/**
 * 司令官
 */
abstract class Commander implements CommanderIF
{
    private $currentResult;

    /**
     * 新規リクエストを生成する
     */
    public function newRequest ( )
    {
        $request = new Request ($this);
        return $request;
    }

    /**
     * リクエストを受け付ける
     */
    abstract public function recieveRequest(Request $request);

}
