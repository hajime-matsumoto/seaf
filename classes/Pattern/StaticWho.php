<?php
namespace Seaf\Pattern;

use Seaf\Exception;

/**
 */
trait StaticWho
{
    public static function who ( )
    {
        return __CLASS__;
    }
}
