<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Model;

use Seaf\Module\Model\Table;

/**
 * @SeafModel UserPre
 */
class UserPreTable extends Table
{
    public static function who ( )
    {
        return __CLASS__;
    }

    public function __construct ( )
    {
        parent::__construct('UserPre');
    }
}
