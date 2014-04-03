<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;

/**
 * DBハンドラを所有する
 */
interface HaveHandlerIF
{
    /**
     * データベースハンドラをセット
     *
     * @param Handler
     */
    public function setHandler ($handler);

    /**
     * データベースハンドラを取得
     *
     * @return Handler
     */
    public function getHandler ( );

    /**
     * データベースハンドラがあるか
     *
     * @return bool
     */
    public function haveHandler ( );
}
