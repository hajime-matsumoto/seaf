<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB\Engine;

use Seaf;
use Seaf\Module\DB\DataTypes;
use Seaf\Module\DB\Result;
use Seaf\Util\ArrayHelper;
use Sqlite3;

/**
 * Sqlite::DBエンジン
 */
class SqliteEngine extends Base
{
    private $db;
    private $path;

    public function initEngine ($dsn)
    {
        $this->path = $dsn;
    }

    protected function con( )
    {
        if ($this->db) return $this->db;
        return $this->db = new Sqlite3($this->path);
    }

    protected function _execute ($sql)
    {
        $result = $this->con()->query($sql);
        if ($result == false) {
            Seaf::Logger('DB')->warn($this->con()->lastErrorMsg());
        }
        return $result;
    }

    protected function _escape ($value, $type)
    {
        $escaped_value = $this->con()->escapeString($value);
        switch ($type) {
        case DataTypes::DATA_INT:
            return $escaped_value;
            break;
        case DataTypes::DATA_STR:
        default:
            return "'$escaped_value'";
            break;
        }
    }

    protected function _fetchAssoc ($result)
    {
        return $result->fetchArray(SQLITE3_ASSOC);
    }
}
