<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * メッセージモジュール
 */
namespace Seaf\Message;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;

/**
 * モジュールファサード
 */
class MessageFacade implements BackEnd\Module\ModuleFacadeIF
{
    use BackEnd\Module\ModuleFacadeTrait;

    public function getTranslator( )
    {
        $rfm = new  \ReflectionMethod($this, 'translator');
        return $rfm->getClosure($this);
    }

    public function translator($key)
    {
        return "[[$key]]";
    }
}
