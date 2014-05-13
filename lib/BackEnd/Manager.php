<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * バックエンドシステム
 */
namespace Seaf\BackEnd;

use Seaf\Base\Proxy;
use Seaf\Util\Util;
use Seaf\Base\DI;
use Seaf\Base\Event;
use Seaf\Logging;

use Seaf\Base\Module;

/**
 * リクエスト
 */
class Manager implements Module\ModuleMediatorIF
{
    use Module\ModuleMediatorTrait;

    protected static $object_name = 'BackEnd';

    private static $instance;

    public function __get($name)
    {
        return $this->makeRequest($name);
    }

    public static function getSingleton( )
    {
        if (!static::$instance) {
            static::$instance = new Manager();
        }
        return static::$instance;
    }

    /**
     * コンストラクタ
     */
    private function __construct( )
    {
    }
}
