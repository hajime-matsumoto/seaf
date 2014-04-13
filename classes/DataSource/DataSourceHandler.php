<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource;

use Seaf;
use Seaf\Base;

class DataSourceHandler
{
    use Base\SeafAccessTrait;
    use Base\LoggerTrait;

    protected $dsn;

    /**
     * 種類別データソースハンドラの作成
     */
    public static function factory($dsn, DataSource $ds = null)
    {
        $type = $dsn->getType();
        $class = __NAMESPACE__.'\\Handler\\'.ucfirst($type).'Handler';
        return new $class($dsn, $ds);
    }

    /**
     * コンストラクタ
     *
     * @param string|array
     */
    public function __construct (DSN $dsn, $ds)
    {
        $this->dsn = $dsn;
        $this->ds = $ds;
    }

    /**
     * リクエストを受け付ける
     */
    public function execute (Request $request)
    {
        // executeMethod系のメソッドへルーティング
        $method = strtolower($request->getMethod()).'Request';
        $result = $this->$method($request);

        if ($result->isError()) {
            $this->error([$result->getError(), $result->getLog()]);
        }

        $this->debug($result->getLog());

        return $result;
    }

    /**
     * リクエストを作成
     *
     * @return Request
     */
    public function newRequest ($method = false)
    {
        $req = Request::Factory( )->ds($this->ds);
        if ($method != false) $req->method($method);
        return $req;
    }
}
