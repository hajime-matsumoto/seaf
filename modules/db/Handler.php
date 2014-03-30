<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB;

use Seaf;
use Seaf\Util\ArrayHelper;

/**
 * DBハンドラ
 */
class Handler
{
    /**
     * DBハンドラを作成する
     *
     * @param array $config
     * @return Module
     */
    public static function factory ($config = array())
    {
        $get = ArrayHelper::getter();
        $dsn = $get($config, 'dsn');

        return new Handler($dsn);
    }

    /**
     * @var Engine/Base
     */
    private $engine;

    /**
     * @var mixed
     */
    private $result;

    /**
     * コンストラクタ
     *
     * @param string
     */
    public function __construct ($dsn)
    {
        // エンジンタイプを取得する
        $type = substr($dsn, 0, $p = strpos($dsn, '://'));

        // エンジン用のDSNを作る
        $dsn = substr($dsn, $p + 3);

        $engine = Seaf::ReflectionClass(sprintf(
            '%s\\Engine\\%sEngine',
            __NAMESPACE__, ucfirst($type)
        ))->newInstance($dsn);

        $this->engine = $engine;
    }

    /**
     * SQLを準備する
     */
    public function prepare ($sql)
    {
        return new Statement($this, $sql);
    }

    /**
     * SQLを実行する
     */
    public function execute ($sql)
    {
        Seaf::Logger('DB::SQL')->debug("EXECUTED: " . $sql);
        return new Result(
            $this,
            $this->engine->execute($sql)
        );
    }

    /**
     * 結果を取得する
     */
    public function fetch ($result)
    {
        return $this->engine->fetchAssoc($result);
    }

    /**
     * SQLのパラメタをエスケープする
     *
     * @param mixed
     * @param string
     */
    public function escape ($value, $type)
    {
        return $this->engine->escape($value, $type);
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction ( )
    {
        $this->execute('BEGIN');
    }

    /**
     * トランザクションを開始する
     */
    public function rollback ( )
    {
        $this->execute('ROLLBACK');
    }

    /**
     * トランザクションを開始する
     */
    public function commit ( )
    {
        $this->execute('COMMIT');
    }

}
