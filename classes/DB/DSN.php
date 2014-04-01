<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;
use Seaf\Data\Container\ArrayContainer;

/**
 * DSN構造体
 *
 * 構造
 * type://user:passwd@host/table
 */
class DSN
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $type;

    /**
     * コンストラクタ
     *
     * @param string
     */
    public function __construct ($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * タイプを取得する
     *
     * @return string
     */
    public function getType ( )
    {
        if ($this->type) return $this->type;

        // エンジンタイプを取得する
        return $this->type = substr($this->dsn, 0, $p = strpos($this->dsn, '://'));
    }

    /**
     * パースする
     *
     * @param bool 戻り値をArrayContainerにする
     * @return array|ArrayContainer
     */
    public function parse ($useArrayContainer = false)
    {
        $regexp = '|
            (?P<type>.+)://
            (
                (?P<user>[^:]+):
                (?P<passwd>[^@]+)@
                (?P<host>[^:]+):
                (?<port>[^/]+)/
            ){0,1}
            (?<db>.+)
            |x';
        if (preg_match($regexp, $this->dsn, $m)) {
            if ($useArrayContainer) $m = new ArrayContainer($m);
            return $m;
        } else {
            throw new Exception\Exception(array(
                'パースできません。%s',
                $this->dsn
            ));
        }
    }
}
