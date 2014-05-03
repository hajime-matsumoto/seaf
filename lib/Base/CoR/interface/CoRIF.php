<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\CoR;

/**
 * Chain Of Responsibilityパターン 
 *
 */
interface CoRIF
{
    /**
     * ネクストを登録
     *
     * @param CoRIF
     */
    public function setNext(CoRIF $next);

    /**
     * ネクストを取得
     *
     * @return CoRIF
     */
    public function getNext( );
}
