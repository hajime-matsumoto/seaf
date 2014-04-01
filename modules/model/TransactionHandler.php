<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Model;

use Seaf;
use Seaf\Exception;
use Seaf\Pattern;
use Seaf\Util\ArrayHelper;

/**
 * トランザクションハンドラのベースクラス
 */
class TransactionHandler
{
    private static $instance = false;
    private $db; 

    /**
     * シングルトンインターフェイス
     */
    public static function singleton ( )
    {
        return self::$instance? self::$instance: self::$instance = new self();
    }

    public function put ($model)
    {
        $this->getDB( )->insertModel($model);
    }

    public function getDB( )
    {
        if ($this->db) return $this->db;
        return Seaf::DB();
    }

    public function setDB($DB)
    {
        $this->db = $DB;
    }

}
