<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Model;

use Seaf;
use Seaf\DB;

/**
 * ユーザデータ
 *
 * @SeafDataTable user
 * @SeafDataPrimary user_id
 * @SeafDataAutoIncrement true
 */
class User extends DB\Model\Base
{
    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * ID
     *
     * @SeafDataName user_id
     * @SeafDataType int
     * @SeafDataSize 4
     */
    protected $id;

    /**
     * 名前
     *
     * @SeafDataName name
     * @SeafDataType varchar
     * @SeafDataSize 100
     */
    protected $name;
}
