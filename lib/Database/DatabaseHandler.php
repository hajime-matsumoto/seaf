<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\Base\Module;
use Seaf\Util\Util;
use Seaf\Base\Proxy;

/**
 * データベース操作ハンドラの抽象クラス
 */
class DatabaseHandler implements DatabaseHandlerIF,Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;
}
