<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;

/**
 * ステートメント
 */
class Statement implements HaveHandlerIF
{
    use HaveHandler;

    /**
     * @var string
     */
    private $sql;


    /**
     * コンストラクタ
     *
     * @param string
     */
    public function __construct (Handler $handler, $sql)
    {
        $this->setHandler($handler);
        $this->sql = new SqlBuilder($sql);
        $this->sql->setHandler($handler);
    }

    /**
     * データバインド
     */
    public function bindValue ($place, $value, $type = DB::DATA_TYPE_STR)
    {
        $this->sql->bindValue($place, $value, $type);
    }

    /**
     * データタイプの宣言
     */
    public function declear ($place, $type = DB::DATA_TYPE_STR)
    {
        $this->sql->declearColumn($place, $type);
    }


    /**
     * SQLをビルドする
     *
     * @param array
     * @return string
     */
    public function buildSql ($params = [])
    {
        if (!empty($params)) {
            foreach ($params as $k=>$v) {
                if ($k{0} != ':') $k=':'.$k;
                $this->bindValue($k, $v);
            }
        }
        return $this->sql->buildSql();
    }

    /**
     * SQLを実行する
     *
     * @return Result
     */
    public function execute ($params = [])
    {
        $sql = $this->buildSql($params);
        return $this->handler->query($sql);
    }
}
