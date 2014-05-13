<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\Base\Module;
use Seaf\Base\Command;
use Seaf\Util\Util;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * データベース操作ハンドラの抽象クラス
 */
class DatabaseHandler implements DatabaseHandlerIF,Event\ObservableIF
{
    use Event\ObservableTrait;
    use Logging\LoggableTrait;

    /**
     * コンストラクタ
     */
    public function __construct (DSN $dsn)
    {
        // DSNを解析する
        $dsn->parse(true)->dump();
    }

    /**
     * ハンドラファクトリ
     */
    public static function factory (DSN $dsn)
    {
        $type = $dsn->getType();

        return Util::ClassName(
            __NAMESPACE__,
            $dsn->getType(),
            'Handler'
        )->newInstance($dsn);
    }

    public function getTable($name)
    {
        $table = new Table($name, $this);
        $table->addObserver($this);
        return $table;
    }
}
