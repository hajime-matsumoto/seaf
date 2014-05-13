<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * モジュール
 */
namespace Seaf\BackEnd\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Event;
use Seaf\BackEnd;
use Seaf\Logging;

/**
 * リクエスト
 */
abstract class ModuleFacade implements ModuleFacadeIF
{
    use ModuleFacadeTrait;
}
