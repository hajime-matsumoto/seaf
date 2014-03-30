<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB;

use Seaf;
use Seaf\Util\ArrayHelper;
use Seaf\Module\DB\DataTypes;

/**
 * SQLステートメント
 */
class Statement
{

    private $handler;
    private $sql;
    private $params;

    /**
     * コンストラクタ
     *
     * @param Handler
     * @param string
     */
    public function __construct ($handler, $sql)
    {
        $this->handler = $handler;
        $this->sql = $sql;
    }

    public function bindValue ($place, $value, $type = DataTypes::DATA_STR)
    {
        $this->params[$place] = array($value, $type);
        return $this;
    }

    public function buildSql ( )
    {
        return preg_replace_callback(
            '/(:[a-zA-Z_]+)/',
            function ($m) {
                $place = $m[1];
                list($value, $type) = $this->params[$place];
                return $this->handler->escape($value, $type);
            },$this->sql
        );
    }

    public function execute ( )
    {
        $result = $this->handler->execute($this->buildSql());
        $this->params = array();
        return $result;
    }
}
