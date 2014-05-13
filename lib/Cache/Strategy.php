<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Cache;

use Seaf\Base\Module;
use Seaf\Util\Util;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * キャッシュストラテジ
 */
abstract class Strategy implements Event\ObservableIF
{
    use Logging\LoggableTrait;
    use Event\ObservableTrait;

    public static function factory ($config = [])
    {
        $c = Util::Dictionary($config);
        $class = Util::ClassName(
            __NAMESPACE__,
            'Strategy',
            $c->get('type', 'memcache')
        );
        return $class->newInstance($config);
    }

    /**
     * キャッシュを作成する
     *
     * @param string
     * @param mixed
     * @param int
     */
    abstract public function createCache($key, $data, $expires = 0);

    /**
     * キャッシュを取得する
     *
     * @param string
     * @param mixed
     * @param int
     */
    abstract public function retriveCache ($key, $until = 0);

    /**
     * キャッシュを破棄する
     *
     * @param string
     */
    abstract public function destroyCache ($key);
}
