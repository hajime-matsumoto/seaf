<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB\Engine;

use Seaf;
use Seaf\Util\ArrayHelper;

/**
 * DBエンジン
 */
abstract class Base
{
    /**
     * コンストラクタ
     */
    public function __construct ($dsn)
    {
        $this->initEngine($dsn);
    }

    public function execute ($sql)
    {
        return $this->_execute($sql);
    }

    public function escape ($value, $type)
    {
        return $this->_escape($value, $type);
    }

    public function fetchAssoc ($result)
    {
        return $this->_fetchAssoc($result);
    }


    abstract public function initEngine($dsn);
}
