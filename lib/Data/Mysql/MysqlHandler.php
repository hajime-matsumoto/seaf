<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\Mysql;

use Seaf\Base;
use Seaf\Data;
use Seaf\Registry\Registry;

use Seaf\Logging;
use Seaf\Event;
/**
 * データベースハンドラ
 */
class MysqlHandler extends Data\DB\ProductHandler
{
    use Logging\LoggingTrait;
    use Event\ObservableTrait;

    /**
     * コンストラクタ
     *
     * @param string|array
     */
    public function __construct (Data\DSN $dsn)
    {
        $p = $dsn->parse(true);

        $this->con = mysqli_connect(
            $p('host', 'localhost'),
            $p('user', 'root'),
            $p('passwd', ''),
            $p('db', ''),
            $p('port', '3306')
        );

        if ($this->con->connect_error) {
            throw new Exception\Exception([
                "DB接続エラー(%s):%s",
                $this->con->connect_errno,
                $this->con->connect_error
            ]);
        }

    }

    /**
     * テーブルを取得する
     */
    public function getTable($name)
    {
        return new Table($this, $name);
    }

    protected function makeResult ($result)
    {
        return new Result($result,$this->con->error);
    }

    public function getLastError ( )
    {
        return mysqli_error($this->con);
    }

    public function escapeVars ($datas)
    {
        if (!is_array($datas)) {
           return is_int($datas) ? intval($datas): mysqli_real_escape_string($this->con, $datas);
        }

        $safeVars = [];

        foreach ($datas as $k=>$v) {
            $safeVars[$k] = is_int($v) ? intval($v): mysqli_real_escape_string($this->con, $v);
        }
        return $safeVars;
    }

    public function query ($query)
    {
        $result = mysqli_query($this->con, $query);
        $this->debug($query,null,['DB','MySQL']);
        if (!$result) {
            $this->warn($query,['error'=>$this->getLastError()],['DB','MySQL']);
        }
        return $result;
    }
}
