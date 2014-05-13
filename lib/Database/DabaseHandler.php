<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\Util\Util;
use Seaf\BackEnd\Module;

/**
 * データベース操作ハンドラの抽象クラス
 */
class DatabaseHandler implements DatabaseHandlerIF,Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;

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
