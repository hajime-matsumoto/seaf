<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB\Engine;

use Seaf;
use Seaf\Util\ArrayHelper;

/**
 * Pgsql::DBエンジン
 */
class PgsqlEngine extends Base
{
    public function initEngine ($dsn)
    {
        var_Dump($dsn);
    }
}
